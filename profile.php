

<?php
session_start();
include "db.php";

// Display message if passed from other pages
$message = "";
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}

// Get user's posts if logged in
$user_posts = [];
$reports_against_user = [];
if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['User_Id'];
    
    // Get user's posts
    $posts_sql = "SELECT Post_Id, Title, content, Date, upvotes, downvotes 
                  FROM posts 
                  WHERE User_Id = $user_id 
                  ORDER BY Date DESC";
    $posts_result = $conn->query($posts_sql);
    if ($posts_result) {
        while($row = $posts_result->fetch_assoc()) {
            $user_posts[] = $row;
        }
    }
    
    // Get reports against this user
    $reports_sql = "SELECT r.reason, r.Date, r.status, u.Name as reporter_name
                    FROM reports r
                    JOIN users u ON r.Reporter_Id = u.User_Id
                    WHERE r.Reported_User_Id = $user_id
                    ORDER BY r.Date DESC";
    $reports_result = $conn->query($reports_sql);
    if ($reports_result) {
        while($row = $reports_result->fetch_assoc()) {
            $reports_against_user[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile - BeyondBorders</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .profile-info { 
            background: #e8f4fd; 
            padding: 20px; 
            border-radius: 10px; 
            margin-bottom: 20px; 
        }
        .profile-info h3 { color: #2c3e50; margin-bottom: 15px; }
        .profile-info p { margin-bottom: 10px; }
        .status { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .warning { background: #fff3cd; color: #856404; }
        .success { background: #d4edda; color: #155724; }
        .nav-link { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; }
        .btn { 
            background: #3498db; 
            color: white; 
            padding: 10px 20px; 
            text-decoration: none; 
            border-radius: 5px; 
            display: inline-block;
            margin-right: 10px;
        }
        .btn:hover { background: #2980b9; }
        .posts-section, .reports-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .post-item {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }
        .report-item {
            background: #fdf2f2;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            border-left: 4px solid #e74c3c;
        }
        .post-title {
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .post-meta {
            color: #7f8c8d;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        .post-content {
            color: #34495e;
            line-height: 1.6;
        }
        .post-actions {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e9ecef;
        }
        .delete-btn {
            color: #e74c3c;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 0.9em;
        }
        .delete-btn:hover {
            background: #fdeaea;
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
        .tabs {
            display: flex;
            background: #ecf0f1;
            border-radius: 10px;
            margin-bottom: 20px;
            padding: 5px;
        }
        .tab {
            flex: 1;
            padding: 10px;
            text-align: center;
            background: none;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: all 0.2s;
        }
        .tab.active {
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <a href="index.php" class="nav-link">← Back to Dashboard</a>
   
    <h2>Profile Information</h2>
    
    <?php if ($message): ?>
        <div class="status success">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
   
    <?php if (!isset($_SESSION['user'])): ?>
        <div class="status warning">
            <p>You are not logged in. Don't have an account? <a href="signup.php">Sign up</a> | <a href="login.php">Log in</a></p>
        </div>
    <?php else: ?>
        <div class="profile-info">
            <h3>User Profile</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['user']['Name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user']['Email']); ?></p>
            <p><strong>User ID:</strong> <?php echo $_SESSION['user']['User_Id']; ?></p>
            <p><strong>MSC University:</strong> <?php echo htmlspecialchars($_SESSION['user']['MSC_university'] ?: 'Not specified'); ?></p>
            
            <?php if (isset($_SESSION['user']['bio']) && !empty($_SESSION['user']['bio'])): ?>
                <p><strong>Bio:</strong> <?php echo nl2br(htmlspecialchars($_SESSION['user']['bio'])); ?></p>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['user']['cv_info']) && !empty($_SESSION['user']['cv_info'])): ?>
                <p><strong>CV Info:</strong> <?php echo nl2br(htmlspecialchars($_SESSION['user']['cv_info'])); ?></p>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['user']['phone']) && !empty($_SESSION['user']['phone'])): ?>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($_SESSION['user']['phone']); ?></p>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['user']['country']) && !empty($_SESSION['user']['country'])): ?>
                <p><strong>Country:</strong> <?php echo htmlspecialchars($_SESSION['user']['country']); ?></p>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['user']['field_of_study']) && !empty($_SESSION['user']['field_of_study'])): ?>
                <p><strong>Field of Study:</strong> <?php echo htmlspecialchars($_SESSION['user']['field_of_study']); ?></p>
            <?php endif; ?>
           
            <a href="edit_profile.php" class="btn">Edit Profile</a>
        </div>
        
        <div class="tabs">
            <button class="tab active" onclick="showTab('posts')">My Posts (<?php echo count($user_posts); ?>)</button>
            <?php if (!empty($reports_against_user)): ?>
                <button class="tab" onclick="showTab('reports')">Reports Against Me (<?php echo count($reports_against_user); ?>)</button>
            <?php endif; ?>
        </div>
        
        <div id="posts" class="tab-content active">
            <div class="posts-section">
                <h3>My Posts</h3>
                
                <?php if (empty($user_posts)): ?>
                    <p>You haven't posted anything yet. <a href="post.php">Create your first post!</a></p>
                <?php else: ?>
                    <?php foreach ($user_posts as $post): ?>
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
                            <div class="post-actions">
                                <a href="view_post.php?id=<?php echo $post['Post_Id']; ?>">View Post</a> | 
                                <a href="delete_post.php?id=<?php echo $post['Post_Id']; ?>&type=post" class="delete-btn" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (!empty($reports_against_user)): ?>
            <div id="reports" class="tab-content">
                <div class="reports-section">
                    <h3>Reports Against You</h3>
                    <p>These are reports that other users have made against your account:</p>
                    
                    <?php foreach ($reports_against_user as $report): ?>
                        <div class="report-item">
                            <div class="post-meta">
                                Reported by <?php echo htmlspecialchars($report['reporter_name']); ?> on <?php echo date('M j, Y', strtotime($report['Date'])); ?>
                                <span class="status-badge <?php echo $report['status']; ?>"><?php echo ucfirst($report['status']); ?></span>
                            </div>
                            <div class="post-content">
                                <strong>Reason:</strong> <?php echo nl2br(htmlspecialchars($report['reason'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <script>
        function showTab(tabName) {
            // Hide all tab contents
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => content.classList.remove('active'));
            
            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // Show selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }
    </script>
</body>
</html>

