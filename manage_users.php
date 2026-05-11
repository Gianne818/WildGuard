<?php
session_start();
include 'connect.php';
if(!isset($_SESSION['admin_id'])) { header("Location: index.php"); exit(); }

$message = "";

// Handle Hard Delete for Users
if(isset($_POST['btnDeleteUser'])) {
    $delete_id = $_POST['delete_id'];
    
    // 1. Delete dependent records first to prevent foreign key constraint errors
    mysqli_query($connection, "DELETE FROM tblentry_record WHERE user_id = '$delete_id'");
    mysqli_query($connection, "DELETE FROM tblvisit WHERE visitor_id = '$delete_id' OR host_student_id = '$delete_id' OR host_personnel_id = '$delete_id'");
    
    // 2. Delete the base user (ON DELETE CASCADE handles tblstudent, tblpersonnel, tblvisitor automatically)
    if(mysqli_query($connection, "DELETE FROM tbluser WHERE user_id = '$delete_id'")) {
        $message = "<div style='color: green; padding: 10px; background: #e6ffe6; margin-bottom: 15px; border-radius: 10px;'>User and all associated logs permanently deleted.</div>";
    } else {
        $message = "<div style='color: red; padding: 10px; background: #ffe6e6; margin-bottom: 15px; border-radius: 10px;'>Error deleting user.</div>";
    }
}

// Fetch all NON-Admin users
$query = "SELECT * FROM tbluser WHERE user_type != 'Admin' ORDER BY user_type, last_name ASC";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="css/site.css">
    <style>
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 15px; overflow: hidden; }
        th, td { padding: 15px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #6b2020; color: white; }
        .btn-delete { background: #cc0000; color: white; border: none; padding: 8px 15px; cursor: pointer; border-radius: 20px; font-weight: bold; }
    </style>
</head>
<body>

<header class="main-header">
    <div style="display:flex; align-items:center; gap:15px;">
        <a href="dashboard.php" style="background:rgba(255,255,255,0.2); color:#fff; padding:5px 15px; border-radius:20px; text-decoration:none; font-size:14px; border: 1px solid #fff;">&larr; Back to Dashboard</a>
    </div>
    <h1>Manage System Users</h1>
    <div class="datetime"></div>
</header>

<div class="glass-container" style="flex-direction: column; padding: 30px; align-items: stretch; max-width: 1000px; margin: 30px auto; background: rgba(255, 255, 255, 0.85); box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
    
    <h2 style="margin-top: 0; color: #333;">Standard User Directory</h2>
    <?php echo $message; ?>

    <div style="overflow-x: auto; border-radius: 15px; border: 1px solid #eee;">
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Type</th>
                    <th style="text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                    <td><span style="background: #eee; padding: 5px 10px; border-radius: 15px; font-size: 14px;"><?php echo htmlspecialchars($row['user_type']); ?></span></td>
                    <td style="text-align: center;">
                        <form method="post" style="margin: 0;" onsubmit="return confirm('WARNING: This will permanently delete this user and ALL their entry logs. Proceed?');">
                            <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($row['user_id']); ?>">
                            <button type="submit" name="btnDeleteUser" class="btn-delete">Hard Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                
                <?php if(mysqli_num_rows($result) == 0): ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #666;">No standard users found in the system.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>