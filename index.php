

<?php 
session_start(); 
include "db.php";

// Get recent posts for dashboard
$sql = "SELECT p.Post_Id, p.Title, p.content, p.Date, p.upvotes, p.downvotes, 
               u.Name as author_name, u.User_Id as author_id
        FROM posts p
        JOIN users u ON p.User_Id = u.User_Id
        ORDER BY p.Date DESC
        LIMIT 10";
$posts_result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>BeyondBorders Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f4;
        }
        .header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .nav {
            background: #34495e;
            padding: 15px;
            text-align: center;
        }
        .nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .nav a:hover {
            background: #2c3e50;
        }
        .content {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
        }
        .status {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success { background: #d4edda; color: #155724; }
        .warning { background: #fff3cd; color: #856404; }
        
        .post-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .post-card:hover {
            transform: translateY(-2px);
        }
        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .post-title {
            color: #2c3e50;
            margin: 0;
            font-size: 1.3em;
        }
        .post-meta {
            color: #7f8c8d;
            font-size: 0.9em;
        }
        .post-content {
            margin: 15px 0;
            line-height: 1.6;
            color: #34495e;
        }
        .post-actions {
            display: flex;
            gap: 15px;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #ecf0f1;
        }
        .vote-btn {
            background: none;
            border: none;
            color: #7f8c8d;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 5px;
            transition: all 0.2s;
        }
        .vote-btn:hover {
            background: #ecf0f1;
        }
        .vote-btn.upvoted {
            color: #27ae60;
            background: #d5f4e6;
        }
        .vote-btn.downvoted {
            color: #e74c3c;
            background: #fdeaea;
        }
        .action-btn {
            color: #3498db;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background 0.2s;
        }
        .action-btn:hover {
            background: #ecf0f1;
        }
        .delete-btn {
            color: #e74c3c;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background 0.2s;
        }
        .delete-btn:hover {
            background: #fdeaea;
        }
        .posts-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .author-link {
            color: #3498db;
            text-decoration: none;
        }
        .author-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BeyondBorders: Master's & PhD Success Portal Abroad</h1>
    </div>
   
    <div class="nav">
        <a href="index.php">Dashboard</a>
        <?php if(!isset($_SESSION['user'])): ?>
            <a href="signup.php">Sign Up</a>
            <a href="login.php">Log In</a>
        <?php endif; ?>
        <a href="profile.php">Profile Info</a>
        <a href="university.php">University</a>
        <a href="search.php">Search</a>
        <a href="post.php">Post Now</a>
        <?php if(isset($_SESSION['user'])): ?>
            <a href="activities.php">Your Activities</a>
            <a href="logout.php">Logout</a>
        <?php endif; ?>
    </div>
   
    <div class="content">
        <?php if(isset($_SESSION['user'])): ?>
            <div class="status success">
                <strong>Welcome back, <?php echo htmlspecialchars($_SESSION['user']['Name']); ?>!</strong>
                <br>You are logged in as a verified user (ID: <?php echo $_SESSION['user']['User_Id']; ?>)
            </div>
        <?php else: ?>
            <div class="status warning">
                You are not logged in. Please log in to access all features.
            </div>
        <?php endif; ?>
        
        <div class="posts-section">
            <h2>Recent Success Stories & Tips</h2>
            
            <?php if ($posts_result->num_rows > 0): ?>
                <?php while($post = $posts_result->fetch_assoc()): ?>
                    <div class="post-card">
                        <div class="post-header">
                            <h3 class="post-title"><?php echo htmlspecialchars($post['Title']); ?></h3>
                            <div class="post-meta">
                                by <a href="user_profile.php?id=<?php echo $post['author_id']; ?>" class="author-link"><?php echo htmlspecialchars($post['author_name']); ?></a> • <?php echo date('M j, Y', strtotime($post['Date'])); ?>
                            </div>
                        </div>
                        
                        <div class="post-content">
                            <?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 300))); ?>
                            <?php if(strlen($post['content']) > 300): ?>
                                <a href="view_post.php?id=<?php echo $post['Post_Id']; ?>">... Read more</a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="post-actions">
                            <?php if(isset($_SESSION['user'])): ?>
                                <!-- Vote buttons -->
                                <button class="vote-btn" onclick="vote(<?php echo $post['Post_Id']; ?>, 'upvote')">
                                    ▲ <?php echo $post['upvotes']; ?>
                                </button>
                                <button class="vote-btn" onclick="vote(<?php echo $post['Post_Id']; ?>, 'downvote')">
                                    ▼ <?php echo $post['downvotes']; ?>
                                </button>
                            <?php else: ?>
                                <span class="vote-btn">▲ <?php echo $post['upvotes']; ?></span>
                                <span class="vote-btn">▼ <?php echo $post['downvotes']; ?></span>
                            <?php endif; ?>
                            
                            <a href="view_post.php?id=<?php echo $post['Post_Id']; ?>" class="action-btn">💬 Comments</a>
                            <a href="share.php?id=<?php echo $post['Post_Id']; ?>" class="action-btn">📤 Share</a>
                            
                            <?php if(isset($_SESSION['user'])): ?>
                                <?php if($_SESSION['user']['User_Id'] != $post['author_id']): ?>
                                    <a href="report.php?type=user&id=<?php echo $post['author_id']; ?>" class="action-btn">🚩 Report</a>
                                <?php else: ?>
                                    <a href="delete_post.php?id=<?php echo $post['Post_Id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this post?')">🗑️ Delete</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No posts yet. Be the first to share your success story!</p>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function vote(postId, voteType) {
            fetch('vote.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `post_id=${postId}&vote_type=${voteType}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error voting');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error processing vote');
            });
        }
    </script>
</body>
</html>

