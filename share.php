

<?php
session_start();
include "db.php";

$post_id = intval($_GET['id']);

// Get post details
$sql = "SELECT p.Title, u.Name as author_name
        FROM posts p
        JOIN users u ON p.User_Id = u.User_Id
        WHERE p.Post_Id = $post_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$post = $result->fetch_assoc();

// Create the proper share URL - adjust this path based on your server setup
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['REQUEST_URI']);
$share_url = $protocol . "://" . $host . $path . "/view_post.php?id=" . $post_id;

// If accessed with action=redirect, redirect to the post
if (isset($_GET['action']) && $_GET['action'] == 'redirect') {
    header("Location: view_post.php?id=" . $post_id);
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Share Post - BeyondBorders</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .nav-link { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; }
        .share-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .share-url {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin: 20px 0;
            font-family: monospace;
            word-break: break-all;
            text-align: left;
        }
        .copy-btn, .test-btn {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
            text-decoration: none;
            display: inline-block;
        }
        .copy-btn:hover, .test-btn:hover { background: #2980b9; }
        .test-btn { background: #27ae60; }
        .test-btn:hover { background: #229954; }
        .social-buttons {
            margin-top: 20px;
        }
        .social-btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
        }
        .facebook { background: #3b5998; }
        .twitter { background: #1da1f2; }
        .linkedin { background: #0077b5; }
        .whatsapp { background: #25d366; }
        .email { background: #34495e; }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            display: none;
        }
    </style>
</head>
<body>
    <a href="view_post.php?id=<?php echo $post_id; ?>" class="nav-link">← Back to Post</a>
    
    <div class="share-container">
        <h2>Share This Post</h2>
        <h3><?php echo htmlspecialchars($post['Title']); ?></h3>
        <p>by <?php echo htmlspecialchars($post['author_name']); ?></p>
        
        <div class="share-url" id="shareUrl"><?php echo $share_url; ?></div>
        
        <div class="success-message" id="successMessage">
            Link copied to clipboard successfully!
        </div>
        
        <button class="copy-btn" onclick="copyToClipboard()">📋 Copy Link</button>
        <a href="<?php echo $share_url; ?>" class="test-btn" target="_blank">🔗 Test Link</a>
        
        <div class="social-buttons">
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($share_url); ?>" 
               target="_blank" class="social-btn facebook">Share on Facebook</a>
               
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($share_url); ?>&text=<?php echo urlencode('Check out: ' . $post['Title']); ?>" 
               target="_blank" class="social-btn twitter">Share on Twitter</a>
               
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($share_url); ?>" 
               target="_blank" class="social-btn linkedin">Share on LinkedIn</a>
               
            <a href="https://wa.me/?text=<?php echo urlencode('Check out this post: ' . $post['Title'] . ' ' . $share_url); ?>" 
               target="_blank" class="social-btn whatsapp">Share on WhatsApp</a>
               
            <a href="mailto:?subject=<?php echo urlencode('Check out: ' . $post['Title']); ?>&body=<?php echo urlencode('I thought you might be interested in this post: ' . $post['Title'] . ' by ' . $post['author_name'] . "\n\n" . $share_url); ?>" 
               class="social-btn email">Share via Email</a>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            <h4>Quick Share Options:</h4>
            <p>You can also share this link directly: <code>view_post.php?id=<?php echo $post_id; ?></code></p>
        </div>
    </div>
    
    <script>
        function copyToClipboard() {
            const shareUrl = document.getElementById('shareUrl').textContent;
            
            // Try modern clipboard API first
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(shareUrl).then(function() {
                    showSuccessMessage();
                }, function(err) {
                    // Fallback to older method
                    fallbackCopyTextToClipboard(shareUrl);
                });
            } else {
                // Fallback for older browsers
                fallbackCopyTextToClipboard(shareUrl);
            }
        }
        
        function fallbackCopyTextToClipboard(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    showSuccessMessage();
                } else {
                    alert('Unable to copy link. Please copy it manually.');
                }
            } catch (err) {
                alert('Unable to copy link. Please copy it manually.');
            }
            
            document.body.removeChild(textArea);
        }
        
        function showSuccessMessage() {
            const message = document.getElementById('successMessage');
            message.style.display = 'block';
            setTimeout(function() {
                message.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>

