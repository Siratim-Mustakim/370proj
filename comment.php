<?php
session_start();
include "db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_id = intval($_POST['post_id']);
    $comment_text = mysqli_real_escape_string($conn, $_POST['comment_text']);
    $user_id = $_SESSION['user']['User_Id'];
    
    if (empty($comment_text)) {
        echo json_encode(['success' => false, 'message' => 'Comment cannot be empty']);
        exit();
    }
    
    $sql = "INSERT INTO comments (Post_Id, User_Id, comment_text) VALUES ($post_id, $user_id, '$comment_text')";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Comment added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding comment']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>