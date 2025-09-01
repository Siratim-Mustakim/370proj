<?php
session_start();
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $msc_university = mysqli_real_escape_string($conn, $_POST['msc_university']);
   
    // Check if email already exists
    $check_sql = "SELECT Email FROM users WHERE Email = '$email'";
    $check_result = $conn->query($check_sql);
   
    if ($check_result->num_rows > 0) {
        $message = "❌ Email already exists! Please use a different email.";
    } else {
        $sql = "INSERT INTO users (Name, Email, Password, MSC_university)
                VALUES ('$name', '$email', '$password', '$msc_university')";
       
        if ($conn->query($sql)) {
            $message = "✅ Sign Up successful! <a href='login.php'>Log In now</a>";
           
            // Update university table if new university
            if (!empty($msc_university)) {
                $uni_check = "SELECT U_Id FROM university WHERE Name = '$msc_university'";
                $uni_result = $conn->query($uni_check);
               
                if ($uni_result->num_rows == 0) {
                    // Generate new university ID
                    $count_sql = "SELECT COUNT(*) as count FROM university";
                    $count_result = $conn->query($count_sql);
                    $count = $count_result->fetch_assoc()['count'] + 1;
                    $u_id = "UNI" . str_pad($count, 3, "0", STR_PAD_LEFT);
                   
                    $insert_uni = "INSERT INTO university (U_Id, Name, total_success) VALUES ('$u_id', '$msc_university', 1)";
                    $conn->query($insert_uni);
                } else {
                    // Update existing university success count
                    $update_uni = "UPDATE university SET total_success = total_success + 1 WHERE Name = '$msc_university'";
                    $conn->query($update_uni);
                }
            }
        } else {
            $message = "❌ Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up - BeyondBorders</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;
        }
        button { background: #3498db; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #2980b9; }
        .message { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .nav-link { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; }
    </style>
</head>
<body>
    <a href="index.php" class="nav-link">← Back to Dashboard</a>
   
    <?php if ($message): ?>
        <div class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
   
    <form method="POST">
        <h2>Sign Up</h2>
       
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
       
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
       
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
       
        <div class="form-group">
            <label for="msc_university">MSC University (if any):</label>
            <input type="text" id="msc_university" name="msc_university" placeholder="Leave blank if none">
        </div>
       
        <button type="submit">Sign Up</button>
    </form>
   
    <p style="margin-top: 20px;">
        Already have an account? <a href="login.php">Log in here</a>
    </p>
</body>
</html>