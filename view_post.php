

<?php
session_start();
include "db.php";

$post_id = intval($_GET['id']);
$message = "";

// Display message if passed from other pages
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}

// Get post details
$sql = "SELECT p.Post_Id, p.Title, p.content, p.Date, p.upvotes, p.downvotes, 
               u.Name as author_name, u.User_Id as author_id
        FROM posts p
        JOIN users u ON p.User_Id = u.User_Id
        WHERE p.Post_Id = $post_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$post = $result->fetch_assoc();

// Get comments
$comments_sql = "SELECT c.Comment_Id, c.comment_text, c.Date, c.User_Id, u.Name as commenter_name
                 FROM comments c
                 JOIN users u ON c.User_Id = u.User_Id
                 WHERE c.Post_Id = $post_id
                 ORDER BY c.Date ASC";
$comments_result = $conn->query($comments_sql);

// Handle comment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user'])) {
    $comment_text = mysqli_real_escape_string($conn, $_POST['comment_text']);
    $user_id = $_SESSION['user']['User_Id'];
    
    $insert_sql = "INSERT INTO comments (Post_Id, User_Id, comment_text) VALUES ($post_id, $user_id, '$comment_text')";
    
    if ($conn->query($insert_sql)) {
        $message = "✅ Comment added successfully!";
        // Refresh comments
        $comments_result = $conn->query($comments_sql);
    } else {
        $message = "❌ Error adding comment: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($post['Title']); ?> - BeyondBorders</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .nav-link { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; }
        .post-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .post-title { color: #2c3e50; margin-bottom: 10px; }
        .post-meta { color: #7f8c8d; margin-bottom: 20px; }
        .post-content { line-height: 1.6; color: #34495e; margin-bottom: 20px; }
        .post-actions {
            display: flex;
            gap: 15px;
            align-items: center;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
        }
        .vote-btn {
            background: none;
            border: none;
            color: #7f8c8d;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.2s;
        }
        .vote-btn:hover { background: #ecf0f1; }
        .action-btn {
            color: #3498db;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.2s;
        }
        .action-btn:hover { background: #ecf0f1; }
        .delete-btn {
            color: #e74c3c;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.2s;
        }
        .delete-btn:hover { background: #fdeaea; }
        
        .comments-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .comment-item {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            border-left: 3px solid #3498db;
        }
        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .comment-author { font-weight: bold; color: #2c3e50; }
        .comment-date { color: #7f8c8d; font-size: 0.9em; }
        .comment-text { margin-top: 10px; line-height: 1.5; }
        .comment-actions {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e9ecef;
        }
        
        .comment-form {
            background: #e8f4fd;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        textarea {
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; resize: vertical;
        }
        button { background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #2980b9; }
        .message { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
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
    <a href="index.php" class="nav-link">← Back to Dashboard</a>
    
    <?php if ($message): ?>
        <div class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="post-container">
        <h1 class="post-title"><?php echo htmlspecialchars($post['Title']); ?></h1>
        <div class="post-meta">
            by <a href="user_profile.php?id=<?php echo $post['author_id']; ?>" class="author-link"><?php echo htmlspecialchars($post['author_name']); ?></a> • <?php echo date('M j, Y \a\t g:i A', strtotime($post['Date'])); ?>
        </div>
        
        <div class="post-content">
            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
        </div>
        
        <div class="post-actions">
            <?php if(isset($_SESSION['user'])): ?>
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
            
            <a href="share.php?id=<?php echo $post['Post_Id']; ?>" class="action-btn">📤 Share</a>
            
            <?php if(isset($_SESSION['user'])): ?>
                <?php if($_SESSION['user']['User_Id'] != $post['author_id']): ?>
                    <a href="report.php?type=user&id=<?php echo $post['author_id']; ?>" class="action-btn">🚩 Report</a>
                <?php else: ?>
                    <a href="delete_post.php?id=<?php echo $post['Post_Id']; ?>&type=post" class="delete-btn" onclick="return confirm('Are you sure you want to delete this post?')">🗑️ Delete Post</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="comments-section">
        <h3>Comments (<?php echo $comments_result->num_rows; ?>)</h3>
        
        <?php if ($comments_result->num_rows > 0): ?>
            <?php while($comment = $comments_result->fetch_assoc()): ?>
                <div class="comment-item">
                    <div class="comment-header">
                        <div>
                            <span class="comment-author">
                                <a href="user_profile.php?id=<?php echo $comment['User_Id']; ?>" class="author-link">
                                    <?php echo htmlspecialchars($comment['commenter_name']); ?>
                                </a>
                            </span>
                            <span class="comment-date"><?php echo date('M j, Y \a\t g:i A', strtotime($comment['Date'])); ?></span>
                        </div>
                        <?php if(isset($_SESSION['user']) && $_SESSION['user']['User_Id'] == $comment['User_Id']): ?>
                            <a href="delete_post.php?id=<?php echo $comment['Comment_Id']; ?>&type=comment" class="delete-btn" onclick="return confirm('Are you sure you want to delete this comment?')" title="Delete Comment">🗑️</a>
                        <?php endif; ?>
                    </div>
                    <div class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No comments yet. Be the first to comment!</p>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['user'])): ?>
            <div class="comment-form">
                <h4>Add a Comment</h4>
                <form method="POST">
                    <textarea name="comment_text" rows="4" placeholder="Share your thoughts..." required></textarea>
                    <br><br>
                    <button type="submit">Post Comment</button>
                </form>
            </div>
        <?php else: ?>
            <p><a href="login.php">Log in</a> to add comments.</p>
        <?php endif; ?>
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

