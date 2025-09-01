<?php
session_start();
include "db.php";

$search_results = [];
$search_query = "";
$search_type = "user";

if (isset($_GET['q']) && isset($_GET['type'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['q']);
    $search_type = $_GET['type'];
   
    if ($search_type == 'user') {
        $sql = "SELECT User_Id, Name, Email, MSC_university, bio
                FROM users
                WHERE Name LIKE '%$search_query%'
                OR Email LIKE '%$search_query%'
                OR MSC_university LIKE '%$search_query%'
                OR bio LIKE '%$search_query%'";
        $result = $conn->query($sql);
       
        while($row = $result->fetch_assoc()) {
            $search_results[] = [
                'type' => 'User',
                'title' => $row['Name'],
                'details' => "Email: " . $row['Email'] . ", ID: " . $row['User_Id'] . ", University: " . ($row['MSC_university'] ?: 'None'),
                'description' => $row['bio'] ? substr($row['bio'], 0, 100) . '...' : ''
            ];
        }
       
    } elseif ($search_type == 'university') {
        $sql = "SELECT U_Id, Name, total_success
                FROM university
                WHERE Name LIKE '%$search_query%'";
        $result = $conn->query($sql);
       
        while($row = $result->fetch_assoc()) {
            $search_results[] = [
                'type' => 'University',
                'title' => $row['Name'],
                'details' => "ID: " . $row['U_Id'] . ", Total Success: " . $row['total_success'],
                'description' => ''
            ];
        }
       
        // Also search in user MSC_university field
        $sql2 = "SELECT DISTINCT MSC_university, COUNT(*) as user_count
                 FROM users
                 WHERE MSC_university LIKE '%$search_query%'
                 AND MSC_university IS NOT NULL
                 AND MSC_university != ''
                 GROUP BY MSC_university";
        $result2 = $conn->query($sql2);
       
        while($row = $result2->fetch_assoc()) {
            $search_results[] = [
                'type' => 'University (from profiles)',
                'title' => $row['MSC_university'],
                'details' => "Users: " . $row['user_count'],
                'description' => ''
            ];
        }
       
    } elseif ($search_type == 'post') {
        $sql = "SELECT p.Post_Id, p.Title, p.content, p.Date, p.upvotes, p.downvotes, u.Name as author_name
                FROM posts p
                JOIN users u ON p.User_Id = u.User_Id
                WHERE p.Title LIKE '%$search_query%'
                OR p.content LIKE '%$search_query%'
                ORDER BY p.Date DESC";
        $result = $conn->query($sql);
       
        while($row = $result->fetch_assoc()) {
            $search_results[] = [
                'type' => 'Post',
                'title' => $row['Title'],
                'details' => "ID: " . $row['Post_Id'] . ", Author: " . $row['author_name'] . ", Date: " . date('M j, Y', strtotime($row['Date'])),
                'description' => substr($row['content'], 0, 150) . '...',
                'post_id' => $row['Post_Id'],
                'votes' => "▲ " . $row['upvotes'] . " ▼ " . $row['downvotes']
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Search - BeyondBorders</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], select {
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;
        }
        button { background: #3498db; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #2980b9; }
        .nav-link { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; }
        .result-item {
            background: #f8f9fa;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }
        .result-item h4 { margin-bottom: 5px; color: #2c3e50; }
        .result-details { color: #7f8c8d; font-size: 0.9em; margin-bottom: 10px; }
        .result-description { color: #34495e; line-height: 1.5; }
        .search-form { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .post-link { color: #3498db; text-decoration: none; }
        .post-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <a href="index.php" class="nav-link">← Back to Dashboard</a>
   
    <div class="search-form">
        <h2>Search</h2>
        <p>Search for users, universities, or posts:</p>
       
        <form method="GET">
            <div class="form-group">
                <label for="type">Search Type:</label>
                <select id="type" name="type">
                    <option value="user" <?php echo $search_type == 'user' ? 'selected' : ''; ?>>Users</option>
                    <option value="university" <?php echo $search_type == 'university' ? 'selected' : ''; ?>>Universities</option>
                    <option value="post" <?php echo $search_type == 'post' ? 'selected' : ''; ?>>Posts</option>
                </select>
            </div>
           
            <div class="form-group">
                <label for="q">Search Query:</label>
                <input type="text" id="q" name="q" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Enter search terms..." required>
            </div>
           
            <button type="submit">Search</button>
        </form>
    </div>
   
    <?php if ($search_query): ?>
        <h3>Search Results for "<?php echo htmlspecialchars($search_query); ?>" in <?php echo ucfirst($search_type); ?>s:</h3>
       
        <?php if (empty($search_results)): ?>
            <p>No results found for your search.</p>
        <?php else: ?>
            <?php foreach ($search_results as $result): ?>
                <div class="result-item">
                    <h4>
                        <?php if ($result['type'] == 'Post' && isset($result['post_id'])): ?>
                            <a href="view_post.php?id=<?php echo $result['post_id']; ?>" class="post-link">
                                <?php echo $result['type']; ?>: <?php echo htmlspecialchars($result['title']); ?>
                            </a>
                        <?php else: ?>
                            <?php echo $result['type']; ?>: <?php echo htmlspecialchars($result['title']); ?>
                        <?php endif; ?>
                    </h4>
                    <div class="result-details">
                        <?php echo htmlspecialchars($result['details']); ?>
                        <?php if (isset($result['votes'])): ?>
                            • <?php echo $result['votes']; ?>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($result['description'])): ?>
                        <div class="result-description"><?php echo htmlspecialchars($result['description']); ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>