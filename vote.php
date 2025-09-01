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
    $vote_type = $_POST['vote_type'];
    $user_id = $_SESSION['user']['User_Id'];
    
    if (!in_array($vote_type, ['upvote', 'downvote'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid vote type']);
        exit();
    }
    
    // Check if user already voted on this post
    $check_sql = "SELECT vote_type FROM votes WHERE Post_Id = $post_id AND User_Id = $user_id";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $existing_vote = $check_result->fetch_assoc()['vote_type'];
        
        if ($existing_vote == $vote_type) {
            // Remove vote if same type
            $delete_sql = "DELETE FROM votes WHERE Post_Id = $post_id AND User_Id = $user_id";
            $conn->query($delete_sql);
            
            // Update post count
            $column = $vote_type == 'upvote' ? 'upvotes' : 'downvotes';
            $update_sql = "UPDATE posts SET $column = $column - 1 WHERE Post_Id = $post_id AND $column > 0";
            $conn->query($update_sql);
        } else {
            // Change vote type
            $update_vote_sql = "UPDATE votes SET vote_type = '$vote_type' WHERE Post_Id = $post_id AND User_Id = $user_id";
            $conn->query($update_vote_sql);
            
            // Update post counts
            if ($vote_type == 'upvote') {
                $update_sql = "UPDATE posts SET upvotes = upvotes + 1, downvotes = GREATEST(downvotes - 1, 0) WHERE Post_Id = $post_id";
            } else {
                $update_sql = "UPDATE posts SET downvotes = downvotes + 1, upvotes = GREATEST(upvotes - 1, 0) WHERE Post_Id = $post_id";
            }
            $conn->query($update_sql);
        }
    } else {
        // Add new vote
        $insert_sql = "INSERT INTO votes (Post_Id, User_Id, vote_type) VALUES ($post_id, $user_id, '$vote_type')";
        $conn->query($insert_sql);
        
        // Update post count
        $column = $vote_type == 'upvote' ? 'upvotes' : 'downvotes';
        $update_sql = "UPDATE posts SET $column = $column + 1 WHERE Post_Id = $post_id";
        $conn->query($update_sql);
    }
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>