

<?php
session_start();
include "db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['User_Id'];

// Get user's posts
$posts_sql = "SELECT Post_Id, Title, content, Date, upvotes, downvotes 
              FROM posts 
              WHERE User_Id = $user_id 
              ORDER BY Date DESC";
$posts_result = $conn->query($posts_sql);

// Get user's comments
$comments_sql = "SELECT c.comment_text, c.Date, p.Title as post_title, p.Post_Id
                 FROM comments c
                 JOIN posts p ON c.Post_Id = p.Post_Id
                 WHERE c.User_Id = $user_id
                 ORDER BY c.Date DESC";
$comments_result = $conn->query($comments_sql);

// Get user's votes
$votes_sql = "SELECT v.vote_type, v.Date, p.Title as post_title, p.Post_Id
              FROM votes v
              JOIN posts p ON v.Post_Id = p.Post_Id
              WHERE v.User_Id = $user_id
              ORDER BY v.Date DESC";
$votes_result = $conn->query($votes_sql);

// Get reports made by user
$reports_made_sql = "SELECT r.reason, r.Date, r.status, u.Name as reported_user_name
                     FROM reports r
                     JOIN users u ON r.Reported_User_Id = u.User_Id
                     WHERE r.Reporter_Id = $user_id
                     ORDER BY r.Date DESC";
$reports_made_result = $conn->query($reports_made_sql);

// Get reports against user
$reports_against_sql = "SELECT r.reason, r.Date, r.status, u.Name as reporter_name
                        FROM reports r
                        JOIN users u ON r.Reporter_Id = u.User_Id
                        WHERE r.Reported_User_Id = $user_id
                        ORDER BY r.Date DESC";
$reports_against_result = $conn->query($reports_against_sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Activities - BeyondBorders</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; }
        .nav-link { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; }
        .activity-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .activity-item {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }
        .post-item { border-left-color: #3498db; }
        .comment-item { border-left-color: #27ae60; }
        .vote-item { border-left-color: #f39c12; }
        .report-item { border-left-color: #e74c3c; }
        
        .activity-title {
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .activity-meta {
            color: #7f8c8d;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        .activity-content {
            color: #34495e;
            line-height: 1.6;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #3498db;
        }
        .stat-label {
            color: #7f8c8d;
            margin-top: 5px;
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
        .upvote { color: #27ae60; }
        .downvote { color: #e74c3c; }
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
    
    <h2>Your Activities</h2>
    
    <div class="stats-container">
        <div class="stat-box">
            <div class="stat-number"><?php echo $posts_result->num_rows; ?></div>
            <div class="stat-label">Posts Created</div>
        </div>
        <div class="stat-box">
            <div class="stat-number"><?php echo $comments_result->num_rows; ?></div>
            <div class="stat-label">Comments Made</div>
        </div>
        <div class="stat-box">
            <div class="stat-number"><?php echo $votes_result->num_rows; ?></div>
            <div class="stat-label">Votes Cast</div>
        </div>
        <div class="stat-box">
            <div class="stat-number"><?php echo $reports_made_result->num_rows; ?></div>
            <div class="stat-label">Reports Made</div>
        </div>
    </div>
    
    <div class="tabs">
        <button class="tab active" onclick="showTab('posts')">My Posts</button>
        <button class="tab" onclick="showTab('comments')">My Comments</button>
        <button class="tab" onclick="showTab('votes')">My Votes</button>
        <button class="tab" onclick="showTab('reports')">Reports</button>
    </div>
    
    <div id="posts" class="tab-content active">
        <div class="activity-section">
            <h3>Your Posts</h3>
            <?php if ($posts_result->num_rows == 0): ?>
                <p>You haven't created any posts yet. <a href="post.php">Create your first post!</a></p>
            <?php else: ?>
                <?php while($post = $posts_result->fetch_assoc()): ?>
                    <div class="activity-item post-item">
                        <h4 class="activity-title"><?php echo htmlspecialchars($post['Title']); ?></h4>
                        <div class="activity-meta">
                            Posted on <?php echo date('M j, Y \a\t g:i A', strtotime($post['Date'])); ?> • 
                            ▲ <?php echo $post['upvotes']; ?> ▼ <?php echo $post['downvotes']; ?>
                        </div>
                        <div class="activity-content">
                            <?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 200))); ?>
                            <?php if(strlen($post['content']) > 200): ?>
                                <a href="view_post.php?id=<?php echo $post['Post_Id']; ?>">... Read more</a>
                            <?php endif; ?>
                            <br><br>
                            <a href="view_post.php?id=<?php echo $post['Post_Id']; ?>">View Post</a> | 
                            <a href="delete_post.php?id=<?php echo $post['Post_Id']; ?>" onclick="return confirm('Are you sure?')" style="color: #e74c3c;">Delete</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <div id="comments" class="tab-content">
        <div class="activity-section">
            <h3>Your Comments</h3>
            <?php if ($comments_result->num_rows == 0): ?>
                <p>You haven't made any comments yet.</p>
            <?php else: ?>
                <?php while($comment = $comments_result->fetch_assoc()): ?>
                    <div class="activity-item comment-item">
                        <h4 class="activity-title">Comment on: <?php echo htmlspecialchars($comment['post_title']); ?></h4>
                        <div class="activity-meta">
                            Commented on <?php echo date('M j, Y \a\t g:i A', strtotime($comment['Date'])); ?>
                        </div>
                        <div class="activity-content">
                            <?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?>
                            <br><br>
                            <a href="view_post.php?id=<?php echo $comment['Post_Id']; ?>">View Post</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <div id="votes" class="tab-content">
        <div class="activity-section">
            <h3>Your Votes</h3>
            <?php if ($votes_result->num_rows == 0): ?>
                <p>You haven't cast any votes yet.</p>
            <?php else: ?>
                <?php while($vote = $votes_result->fetch_assoc()): ?>
                    <div class="activity-item vote-item">
                        <h4 class="activity-title">
                            <span class="<?php echo $vote['vote_type']; ?>">
                                <?php echo $vote['vote_type'] == 'upvote' ? '▲ Upvoted' : '▼ Downvoted'; ?>
                            </span>
                            : <?php echo htmlspecialchars($vote['post_title']); ?>
                        </h4>
                        <div class="activity-meta">
                            Voted on <?php echo date('M j, Y \a\t g:i A', strtotime($vote['Date'])); ?>
                        </div>
                        <div class="activity-content">
                            <a href="view_post.php?id=<?php echo $vote['Post_Id']; ?>">View Post</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <div id="reports" class="tab-content">
        <div class="activity-section">
            <h3>Reports You Made</h3>
            <?php if ($reports_made_result->num_rows == 0): ?>
                <p>You haven't made any reports.</p>
            <?php else: ?>
                <?php while($report = $reports_made_result->fetch_assoc()): ?>
                    <div class="activity-item report-item">
                        <h4 class="activity-title">Reported: <?php echo htmlspecialchars($report['reported_user_name']); ?></h4>
                        <div class="activity-meta">
                            Reported on <?php echo date('M j, Y \a\t g:i A', strtotime($report['Date'])); ?>
                            <span class="status-badge <?php echo $report['status']; ?>"><?php echo ucfirst($report['status']); ?></span>
                        </div>
                        <div class="activity-content">
                            <strong>Reason:</strong> <?php echo nl2br(htmlspecialchars($report['reason'])); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
            
            <?php if ($reports_against_result->num_rows > 0): ?>
                <h3 style="margin-top: 30px;">Reports Against You</h3>
                <?php while($report = $reports_against_result->fetch_assoc()): ?>
                    <div class="activity-item report-item">
                        <h4 class="activity-title">Reported by: <?php echo htmlspecialchars($report['reporter_name']); ?></h4>
                        <div class="activity-meta">
                            Reported on <?php echo date('M j, Y \a\t g:i A', strtotime($report['Date'])); ?>
                            <span class="status-badge <?php echo $report['status']; ?>"><?php echo ucfirst($report['status']); ?></span>
                        </div>
                        <div class="activity-content">
                            <strong>Reason:</strong> <?php echo nl2br(htmlspecialchars($report['reason'])); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
    
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

