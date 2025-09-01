<?php
session_start();
include "db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$reported_user_id = intval($_GET['id']);
$reporter_id = $_SESSION['user']['User_Id'];

// Get reported user info
$user_sql = "SELECT Name FROM users WHERE User_Id = $reported_user_id";
$user_result = $conn->query($user_sql);

if ($user_result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$reported_user = $user_result->fetch_assoc();

// Handle report submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    
    if (!empty($reason)) {
        $sql = "INSERT INTO reports (Reporter_Id, Reported_User_Id, reason) 
                VALUES ($reporter_id, $reported_user_id, '$reason')";
        
        if ($conn->query($sql)) {
            $message = "✅ Report submitted successfully. Thank you for helping keep our community safe.";
        } else {
            $message = "❌ Error submitting report: " . $conn->error;
        }
    } else {
        $message = "❌ Please provide a reason for reporting.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Report User - BeyondBorders</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; resize: vertical; }
        button { background: #e74c3c; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #c0392b; }
        .message { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .nav-link { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; }
        .warning-box {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
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
    
    <div class="warning-box">
        <strong>⚠️ Report User</strong><br>
        You are about to report <strong><?php echo htmlspecialchars($reported_user['Name']); ?></strong>. 
        Please only report users who violate community guidelines.
    </div>
   
    <form method="POST">
        <h2>Report User</h2>
       
        <div class="form-group">
            <label for="reason">Reason for reporting:</label>
            <textarea id="reason" name="reason" rows="6" placeholder="Please describe why you are reporting this user..." required></textarea>
        </div>
       
        <button type="submit">Submit Report</button>
    </form>
</body>
</html>