<?php
session_start();
include "db.php";

// Get universities from the university table and also count from users table
$sql = "SELECT u.U_Id, u.Name, u.total_success,
               (SELECT COUNT(*) FROM users WHERE MSC_university = u.Name) as current_users
        FROM university u
        ORDER BY u.total_success DESC";
$result = $conn->query($sql);

// Also get universities that exist in users table but not in university table
$sql2 = "SELECT DISTINCT MSC_university, COUNT(*) as user_count
         FROM users
         WHERE MSC_university IS NOT NULL
         AND MSC_university != ''
         AND MSC_university NOT IN (SELECT Name FROM university)
         GROUP BY MSC_university";
$result2 = $conn->query($sql2);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Universities - BeyondBorders</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #3498db; color: white; }
        tr:hover { background-color: #f5f5f5; }
        .nav-link { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; }
        .university-item {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }
    </style>
</head>
<body>
    <a href="index.php" class="nav-link">← Back to Dashboard</a>
   
    <h2>Universities</h2>
    <p>All universities with their success statistics:</p>
   
    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>University ID</th>
                <th>University Name</th>
                <th>Total Success</th>
                <th>Current Users</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['U_Id']); ?></td>
                <td><?php echo htmlspecialchars($row['Name']); ?></td>
                <td><?php echo $row['total_success']; ?></td>
                <td><?php echo $row['current_users']; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No universities found in the main university table.</p>
    <?php endif; ?>
   
    <?php if ($result2->num_rows > 0): ?>
        <h3>Additional Universities (from user profiles)</h3>
        <p>These universities appear in user profiles but haven't been added to the main university table:</p>
       
        <?php while($row = $result2->fetch_assoc()): ?>
        <div class="university-item">
            <h4><?php echo htmlspecialchars($row['MSC_university']); ?></h4>
            <p><strong>Users from this university:</strong> <?php echo $row['user_count']; ?></p>
        </div>
        <?php endwhile; ?>
    <?php endif; ?>
</body>
</html>