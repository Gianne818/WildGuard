<?php
session_start();
include 'connect.php';

// no guards allowed
if(!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] === 'Guard') { 
    header("Location: dashboard.php"); 
    exit(); 
}

// same stor pagination
$perPage = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// full join
$baseUnion = "(
    SELECT e.entry_id, e.user_id, u.first_name, u.last_name, u.user_type, e.entry_time AS event_time, 'IN' AS event_type
    FROM tblentry_record e
    JOIN tbluser u ON e.user_id = u.user_id
    UNION ALL
    SELECT e.entry_id, e.user_id, u.first_name, u.last_name, u.user_type, e.exit_time AS event_time, 'OUT' AS event_type
    FROM tblentry_record e
    JOIN tbluser u ON e.user_id = u.user_id
    WHERE e.exit_time IS NOT NULL
)";

// $baseUnion = "(SELECT e.entry_id, e.user_id, u.first_name, u.last_name, u.user_type, e.entry_time AS event_time, 'IN' AS event_type
//     FROM tblentry_record e JOIN tbluser u ON e.user_id = u.user_id)";



// Get total count for pagination
$countSql = "SELECT COUNT(*) AS cnt FROM $baseUnion AS t";
$countRes = mysqli_query($connection, $countSql);
$totalRows = 0;
if($countRes){
    $cRow = mysqli_fetch_assoc($countRes);
    $totalRows = (int)$cRow['cnt'];
}
$totalPages = max(1, (int)ceil($totalRows / $perPage));

// Fetch the paginated slice
$query = "SELECT * FROM $baseUnion AS t ORDER BY event_time DESC, event_type DESC LIMIT $perPage OFFSET $offset";
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
    <h1>Master Entry/Exit Log</h1>
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
                    <th>Event</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): 
                    $isOut = $row['event_type'] === 'OUT';
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['entry_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['last_name'] . ', ' . $row['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_type']); ?></td>
                    <td class="<?php echo $isOut ? 'status-out' : 'status-in'; ?>">
                        <?php echo htmlspecialchars($row['event_type']); ?>
                    </td>
                    <td><?php echo date('M d, Y h:i A', strtotime($row['event_time'])); ?></td>
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

    <!-- Pagination controls -->
    <div style="display:flex; justify-content:center; margin-top:16px; gap:8px;">
        <?php if($page > 1): ?>
            <a href="?page=1" class="btn" style="padding:6px 10px; background:#f0f0f0; border-radius:6px; text-decoration:none;">&laquo; First</a>
            <a href="?page=<?php echo $page-1; ?>" class="btn" style="padding:6px 10px; background:#f0f0f0; border-radius:6px; text-decoration:none;">&lsaquo; Prev</a>
        <?php endif; ?>

        <span style="align-self:center; color:#444;">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>

        <?php if($page < $totalPages): ?>
            <a href="?page=<?php echo $page+1; ?>" class="btn" style="padding:6px 10px; background:#f0f0f0; border-radius:6px; text-decoration:none;">Next &rsaquo;</a>
            <a href="?page=<?php echo $totalPages; ?>" class="btn" style="padding:6px 10px; background:#f0f0f0; border-radius:6px; text-decoration:none;">Last &raquo;</a>
        <?php endif; ?>
    </div>

</div>

</body>
</html>