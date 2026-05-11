<?php
session_start();
include 'connect.php';

// Strict RBAC: Kick out Guards (Level 1)
if(!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] === 'Guard') { 
    header("Location: dashboard.php"); 
    exit(); 
}

// Fetch ALL historical records
$query = "SELECT e.entry_id, e.user_id, e.entry_time, e.exit_time, u.first_name, u.last_name, u.user_type 
          FROM tblentry_record e 
          JOIN tbluser u ON e.user_id = u.user_id 
          ORDER BY e.entry_time DESC";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Entry Records</title>
    <link rel="stylesheet" href="css/site.css">
    <style>
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 15px; overflow: hidden; }
        th, td { padding: 15px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #6b2020; color: white; }
        .status-in { color: #009900; font-weight: bold; }
        .status-out { color: #cc0000; font-weight: bold; }
    </style>
</head>
<body>

<header class="main-header">
    <div style="display:flex; align-items:center; gap:15px;">
        <a href="dashboard.php" style="background:rgba(255,255,255,0.2); color:#fff; padding:5px 15px; border-radius:20px; text-decoration:none; font-size:14px; border: 1px solid #fff;">&larr; Back to Dashboard</a>
    </div>
    <h1>Master Entry Log</h1>
    <div class="datetime"></div>
</header>

<div class="glass-container" style="flex-direction: column; padding: 30px; align-items: stretch; max-width: 1000px; margin: 30px auto; background: rgba(255, 255, 255, 0.85); box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
    
    <h2 style="margin-top: 0; color: #333;">Historical Campus Access Records</h2>

    <div style="overflow-x: auto; border-radius: 15px; border: 1px solid #eee; height: 600px; overflow-y: scroll;">
        <table>
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Entry Time</th>
                    <th>Exit Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): 
                    $isOut = !is_null($row['exit_time']);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['entry_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['last_name'] . ', ' . $row['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_type']); ?></td>
                    <td><?php echo date('M d, Y h:i A', strtotime($row['entry_time'])); ?></td>
                    <td><?php echo $isOut ? date('M d, Y h:i A', strtotime($row['exit_time'])) : '---'; ?></td>
                    <td class="<?php echo $isOut ? 'status-out' : 'status-in'; ?>">
                        <?php echo $isOut ? 'OUT' : 'INSIDE'; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                
                <?php if(mysqli_num_rows($result) == 0): ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #666;">No records found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>