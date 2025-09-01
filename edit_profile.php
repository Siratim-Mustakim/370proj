<?php
session_start();
include "db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$user_id = $_SESSION['user']['User_Id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $msc_university = mysqli_real_escape_string($conn, $_POST['msc_university']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $cv_info = mysqli_real_escape_string($conn, $_POST['cv_info']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $field_of_study = mysqli_real_escape_string($conn, $_POST['field_of_study']);
    
    $sql = "UPDATE users SET 
            Name = '$name',
            Email = '$email',
            MSC_university = '$msc_university',
            bio = '$bio',
            cv_info = '$cv_info',
            phone = '$phone',
            country = '$country',
            field_of_study = '$field_of_study'
            WHERE User_Id = $user_id";
    
    if ($conn->query($sql)) {
        // Update session data
        $_SESSION['user']['Name'] = $name;
        $_SESSION['user']['Email'] = $email;
        $_SESSION['user']['MSC_university'] = $msc_university;
        $_SESSION['user']['bio'] = $bio;
        $_SESSION['user']['cv_info'] = $cv_info;
        $_SESSION['user']['phone'] = $phone;
        $_SESSION['user']['country'] = $country;
        $_SESSION['user']['field_of_study'] = $field_of_study;
        
        $message = "✅ Profile updated successfully!";
    } else {
        $message = "❌ Error updating profile: " . $conn->error;
    }
}

// Get current user data
$sql = "SELECT * FROM users WHERE User_Id = $user_id";
$result = $conn->query($sql);
$user_data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile - BeyondBorders</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], textarea {
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;
        }
        textarea { resize: vertical; }
        button { background: #3498db; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #2980b9; }
        .message { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .nav-link { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; }
    </style>
</head>
<body>
    <a href="profile.php" class="nav-link">← Back to Profile</a>
   
    <?php if ($message): ?>
        <div class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
   
    <form method="POST">
        <h2>Edit Profile</h2>
       
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_data['Name']); ?>" required>
        </div>
       
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['Email']); ?>" required>
        </div>
       
        <div class="form-group">
            <label for="msc_university">MSC University:</label>
            <input type="text" id="msc_university" name="msc_university" value="<?php echo htmlspecialchars($user_data['MSC_university']); ?>" placeholder="Your university">
        </div>
        
        <div class="form-group">
            <label for="bio">Bio:</label>
            <textarea id="bio" name="bio" rows="4" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user_data['bio']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="cv_info">CV Information:</label>
            <textarea id="cv_info" name="cv_info" rows="6" placeholder="Add your CV details, experience, achievements..."><?php echo htmlspecialchars($user_data['cv_info']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user_data['phone']); ?>" placeholder="Your phone number">
        </div>
        
        <div class="form-group">
            <label for="country">Country:</label>
            <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($user_data['country']); ?>" placeholder="Your country">
        </div>
        
        <div class="form-group">
            <label for="field_of_study">Field of Study:</label>
            <input type="text" id="field_of_study" name="field_of_study" value="<?php echo htmlspecialchars($user_data['field_of_study']); ?>" placeholder="Your field of study">
        </div>
       
        <button type="submit">Update Profile</button>
    </form>
</body>
</html>