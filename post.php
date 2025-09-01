<?php
session_start();
include "db.php";

$message = "";

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    $not_logged_in = true;
} else {
    $not_logged_in = false;
   
    // Handle post submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $content = mysqli_real_escape_string($conn, $_POST['content']);
        $user_id = $_SESSION['user']['User_Id'];
       
        $sql = "INSERT INTO posts (User_Id, Title, content)
                VALUES ('$user_id', '$title', '$content')";
       
        if ($conn->query($sql)) {
            $message = "✅ Post created successfully!";
        } else {
            $message = "❌ Error creating post: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Post Now - BeyondBorders</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea {
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;
        }
        textarea { resize: vertical; }
        button { background: #3498db; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #2980b9; }
        .message { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .warning { background: #fff3cd; color: #856404; }
        .nav-link { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; }
    </style>
</head>
<body>
    <a href="index.php" class="nav-link">← Back to Dashboard</a>
   
    <h2>Post Now</h2>
   
    <?php if ($not_logged_in): ?>
        <div class="message warning">
            <p>You must be logged in to create posts. Please <a href="login.php">log in</a> first.</p>
            <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
        </div>
    <?php else: ?>
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
       
        <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['user']['Name']); ?></strong>! Share your experience, tips, or questions:</p>
       
        <form method="POST">
            <div class="form-group">
                <label for="title">Post Title:</label>
                <input type="text" id="title" name="title" placeholder="Enter post title..." required>
            </div>
           
            <div class="form-group">
                <label for="content">Content:</label>
                <textarea id="content" name="content" rows="8" placeholder="Share your experience, tips, or questions..." required></textarea>
            </div>
           
            <button type="submit">Create Post</button>
        </form>
    <?php endif; ?>
</body>
</html>