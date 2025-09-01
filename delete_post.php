

<?php
session_start();
include "db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['User_Id'];
$message = "";

// Handle post deletion
if (isset($_GET['id']) && isset($_GET['type']) && $_GET['type'] == 'post') {
    $post_id = intval($_GET['id']);
    
    // Check if user owns this post
    $check_sql = "SELECT User_Id FROM posts WHERE Post_Id = $post_id";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $post_owner = $check_result->fetch_assoc()['User_Id'];
        
        if ($post_owner == $user_id) {
            // Delete the post (cascade will handle related comments, votes)
            $delete_sql = "DELETE FROM posts WHERE Post_Id = $post_id";
            
            if ($conn->query($delete_sql)) {
                $message = "Post deleted successfully!";
                header("Location: profile.php?message=" . urlencode($message));
                exit();
            } else {
                $message = "Error deleting post: " . $conn->error;
            }
        } else {
            $message = "You can only delete your own posts.";
        }
    } else {
        $message = "Post not found.";
    }
}

// Handle comment deletion  
if (isset($_GET['id']) && isset($_GET['type']) && $_GET['type'] == 'comment') {
    $comment_id = intval($_GET['id']);
    
    // Check if user owns this comment
    $check_sql = "SELECT User_Id, Post_Id FROM comments WHERE Comment_Id = $comment_id";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $comment_data = $check_result->fetch_assoc();
        
        if ($comment_data['User_Id'] == $user_id) {
            // Delete the comment
            $delete_sql = "DELETE FROM comments WHERE Comment_Id = $comment_id";
            
            if ($conn->query($delete_sql)) {
                $message = "Comment deleted successfully!";
                header("Location: view_post.php?id=" . $comment_data['Post_Id'] . "&message=" . urlencode($message));
                exit();
            } else {
                $message = "Error deleting comment: " . $conn->error;
            }
        } else {
            $message = "You can only delete your own comments.";
        }
    } else {
        $message = "Comment not found.";
    }
}

// Handle direct post ID for backward compatibility
if (isset($_GET['id']) && !isset($_GET['type'])) {
    $post_id = intval($_GET['id']);
    
    // Check if user owns this post
    $check_sql = "SELECT User_Id FROM posts WHERE Post_Id = $post_id";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $post_owner = $check_result->fetch_assoc()['User_Id'];
        
        if ($post_owner == $user_id) {
            // Delete the post
            $delete_sql = "DELETE FROM posts WHERE Post_Id = $post_id";
            
            if ($conn->query($delete_sql)) {
                $message = "Post deleted successfully!";
                header("Location: profile.php?message=" . urlencode($message));
                exit();
            } else {
                $message = "Error deleting post: " . $conn->error;
            }
        } else {
            $message = "You can only delete your own posts.";
        }
    } else {
        $message = "Post not found.";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete - BeyondBorders</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .message { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; }
        .nav-link { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; }
    </style>
</head>
<body>
    <a href="index.php" class="nav-link">← Back to Dashboard</a>
    
    <?php if ($message): ?>
        <div class="message error">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <p><a href="profile.php">Return to Profile</a></p>
</body>
</html>

