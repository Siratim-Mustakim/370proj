<?php
session_start();
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
   
    $sql = "SELECT * FROM users WHERE Email='$email' AND Password='$password'";
    $result = $conn->query($sql);
   
    if ($result->num_rows > 0) {
        $_SESSION['user'] = $result->fetch_assoc();
        header("Location: index.php");
        exit();
    } else {
        $message = "❌ Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Log In - BeyondBorders</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="email"], input[type="password"] {
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;
        }
        button { background: #3498db; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #2980b9; }
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
   
    <form method="POST">
        <h2>Log In</h2>
       
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
       
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
       
        <button type="submit">Log In</button>
    </form>
   
    <p style="margin-top: 20px;">
        Don't have an account? <a href="signup.php">Sign up here</a>
    </p>
</body>
</html>