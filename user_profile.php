

<?php
session_start();
include "db.php";

$user_id = intval($_GET['id']);

// Get user information
$sql = "SELECT User_Id, Name, Email, MSC_university, bio, cv_info, phone, country, field_of_study, Date_created
        FROM users WHERE User_Id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$user_data = $result->fetch_assoc();

// Get user's posts
$posts_sql = "SELECT Post_Id, Title, content, Date, upvotes, downvotes 
              FROM posts 
              WHERE User_Id = $user_id 
              ORDER BY Date DESC";
$posts_result = $conn->query($posts_sql);

// Get reports against this user (only if current user is logged in)
$reports = [];
if (isset($_SESSION['user'])) {
    $reports_sql = "SELECT r.reason, r.Date, r.status, u.Name as reporter_name
                    FROM reports r
                    JOIN users u ON r.Reporter_Id = u.User_Id
                    WHERE r.Reported_User_Id = $user_id
                    ORDER BY r.Date DESC";
    $reports_result = $conn->query($reports_sql);
    
    if ($reports_result) {
        while($row = $reports_result->fetch_assoc()) {
            $reports[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($user_data['Name']); ?>'s Profile - BeyondBorders</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .nav-link { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; }
        .profile-info { 
            background: #e8f4fd; 
            padding: 20px; 
            border-radius: 10px; 
            margin-bottom: 20px; 
        }
        .profile-info h3 { color: #2c3e50; margin-bottom: 15px; }
        .profile-info p { margin-bottom: 10px; }
        .posts-section, .reports-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .post-item, .report-item {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }
        .report-item {
            border-left-color: #e74c3c;
        }
        .post-title {
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .post-meta, .report-meta {
            color: #7f8c8d;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        .post-content {
            color: #34495e;
            line-height: 1.6;
        }
        .report-btn {
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
        }
        .report-btn:hover {
            background: #c0392b;
        }
        .status-badge {
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 0.8em;
            color: white;
        }
        .pending { background: #f39c12; }
        .reviewed { background: #3498db; }
        .resolved { background: #27ae60; }
    </style>
</head>
<body>
    <a href="index.php" class="nav-link">← Back to Dashboard</a>
    
    <div class="profile-info">
        <h3><?php echo htmlspecialchars($user_data['Name']); ?>'s Profile</h3>
        <p><strong>User ID:</strong> <?php echo $user_data['User_Id']; ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['Email']); ?></p>
        <p><strong>Member since:</strong> <?php echo date('M j, Y', strtotime($user_data['Date_created'])); ?></p>
        <p><strong>MSC University:</strong> <?php echo htmlspecialchars($user_data['MSC_university'] ?: 'Not specified'); ?></p>
        
        <?php if (!empty($user_data['bio'])): ?>
            <p><strong>Bio:</strong> <?php echo nl2br(htmlspecialchars($user_data['bio'])); ?></p>
        <?php endif; ?>
        
        <?php if (!empty($user_data['country'])): ?>
            <p><strong>Country:</strong> <?php echo htmlspecialchars($user_data['country']); ?></p>
        <?php endif; ?>
        
        <?php if (!empty($user_data['field_of_study'])): ?>
            <p><strong>Field of Study:</strong> <?php echo htmlspecialchars($user_data['field_of_study']); ?></p>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['User_Id'] != $user_id): ?>
            <a href="report.php?type=user&id=<?php echo $user_id; ?>" class="report-btn">🚩 Report User</a>
        <?php endif; ?>
    </div>
    
    <div class="posts-section">
        <h3><?php echo htmlspecialchars($user_data['Name']); ?>'s Posts (<?php echo $posts_result->num_rows; ?>)</h3>
        
        <?php if ($posts_result->num_rows == 0): ?>
            <p>This user hasn't posted anything yet.</p>
        <?php else: ?>
            <?php while($post = $posts_result->fetch_assoc()): ?>
                <div class="post-item">
                    <h4 class="post-title"><?php echo htmlspecialchars($post['Title']); ?></h4>
                    <div class="post-meta">
                        Posted on <?php echo date('M j, Y', strtotime($post['Date'])); ?> • 
                        ▲ <?php echo $post['upvotes']; ?> ▼ <?php echo $post['downvotes']; ?>
                    </div>
                    <div class="post-content">
                        <?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 200))); ?>
                        <?php if(strlen($post['content']) > 200): ?>
                            <a href="view_post.php?id=<?php echo $post['Post_Id']; ?>">... Read more</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($reports)): ?>
        <div class="reports-section">
            <h3>Reports Against This User (<?php echo count($reports); ?>)</h3>
            
            <?php foreach($reports as $report): ?>
                <div class="report-item">
                    <div class="report-meta">
                        Reported by <?php echo htmlspecialchars($report['reporter_name']); ?> on <?php echo date('M j, Y', strtotime($report['Date'])); ?>
                        <span class="status-badge <?php echo $report['status']; ?>"><?php echo ucfirst($report['status']); ?></span>
                    </div>
                    <div class="post-content">
                        <strong>Reason:</strong> <?php echo nl2br(htmlspecialchars($report['reason'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>

