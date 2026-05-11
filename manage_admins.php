<?php
session_start();
include 'connect.php';

// Strict RBAC: Kick out Guards (Level 1)
if(!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] === 'Guard') { 
    header("Location: dashboard.php"); 
    exit(); 
}

$isSysAdmin = ($_SESSION['admin_role'] === 'System Admin');
$message = "";

// CREATE Admin/Guard
if(isset($_POST['btnAddAdmin'])) {
    $uid = $_POST['admin_id'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Auto-assign levels based on role to strictly enforce 3-2-1 hierarchy
    if ($isSysAdmin) {
        $role = $_POST['role'];
        if ($role === 'System Admin') $level = 3;
        elseif ($role === 'Security Admin') $level = 2;
        else $level = 1; // Guard
    } else {
        // Security Admins are forced to only create Level 1 Guards
        $role = 'Guard';
        $level = 1;
    }
    
    $check = mysqli_query($connection, "SELECT * FROM tbluser WHERE user_id = '$uid'");
    if(mysqli_num_rows($check) == 0) {
        mysqli_query($connection, "INSERT INTO tbluser (user_id, first_name, last_name, user_type, password) VALUES ('$uid', '$fname', '$lname', 'Admin', '$pass')");
        mysqli_query($connection, "INSERT INTO tbladmin (admin_id, admin_role, access_level) VALUES ('$uid', '$role', $level)");
        $message = "<div style='color: green; padding: 10px; background: #e6ffe6; margin-bottom: 15px; border-radius: 10px;'>Account Created Successfully (Level $level).</div>";
    } else {
        $message = "<div style='color: red; padding: 10px; background: #ffe6e6; margin-bottom: 15px; border-radius: 10px;'>Error: User ID already exists.</div>";
    }
}

// UPDATE Admin/Guard
if(isset($_POST['btnUpdateAdmin'])) {
    $uid = $_POST['update_id'];
    
    if ($isSysAdmin) {
        $role = $_POST['role'];
        if ($role === 'System Admin') $level = 3;
        elseif ($role === 'Security Admin') $level = 2;
        else $level = 1;
    } else {
        $role = 'Guard';
        $level = 1;
    }
    
    mysqli_query($connection, "UPDATE tbladmin SET admin_role = '$role', access_level = $level WHERE admin_id = '$uid'");
    $message = "<div style='color: green; padding: 10px; background: #e6ffe6; margin-bottom: 15px; border-radius: 10px;'>Account Updated Successfully to $role (Level $level).</div>";
}

// DELETE Admin/Guard
if(isset($_POST['btnDeleteAdmin'])) {
    $delete_id = $_POST['delete_id'];
    
    if($delete_id == $_SESSION['admin_id']) {
        $message = "<div style='color: red; padding: 10px; background: #ffe6e6; margin-bottom: 15px; border-radius: 10px;'>Cannot delete your own account.</div>";
    } else {
        mysqli_query($connection, "DELETE FROM tblentry_record WHERE user_id = '$delete_id'");
        mysqli_query($connection, "DELETE FROM tbladmin WHERE admin_id = '$delete_id'");
        mysqli_query($connection, "DELETE FROM tbluser WHERE user_id = '$delete_id'");
        $message = "<div style='color: green; padding: 10px; background: #e6ffe6; margin-bottom: 15px; border-radius: 10px;'>Account permanently deleted.</div>";
    }
}

// READ Logic: System Admins see all. Security Admins see ONLY Guards.
if ($isSysAdmin) {
    $query = "SELECT u.user_id, u.first_name, u.last_name, a.admin_role, a.access_level FROM tbluser u JOIN tbladmin a ON u.user_id = a.admin_id WHERE u.user_type = 'Admin' ORDER BY a.access_level DESC, u.last_name ASC";
} else {
    $query = "SELECT u.user_id, u.first_name, u.last_name, a.admin_role, a.access_level FROM tbluser u JOIN tbladmin a ON u.user_id = a.admin_id WHERE u.user_type = 'Admin' AND a.admin_role = 'Guard' ORDER BY u.last_name ASC";
}
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Personnel</title>
    <link rel="stylesheet" href="css/site.css">
    <style>
        table { width: 100%; border-collapse: collapse; background: #fff; margin-bottom: 20px; border-radius: 15px; overflow: hidden;}
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #6b2020; color: white; }
        .btn-small { padding: 8px 15px; border: none; cursor: pointer; border-radius: 20px; color: white; font-weight: bold; }
        .btn-edit { background: #0066cc; }
        .btn-delete { background: #cc0000; }
        .form-inline input, .form-inline select { padding: 8px; margin-right: 5px; border-radius: 15px; border: 1px solid #ccc; }
    </style>
</head>
<body>

<header class="main-header">
    <div style="display:flex; align-items:center; gap:15px;">
        <a href="dashboard.php" style="background:rgba(255,255,255,0.2); color:#fff; padding:5px 15px; border-radius:20px; text-decoration:none; font-size:14px; border: 1px solid #fff;">&larr; Back to Dashboard</a>
    </div>
    <h1><?php echo $isSysAdmin ? 'Manage System Administrators' : 'Manage Security Guards'; ?></h1>
    <div class="datetime"></div>
</header>

<div class="glass-container" style="flex-direction: column; padding: 30px; align-items: stretch; max-width: 1000px; margin: 30px auto; background: rgba(255, 255, 255, 0.85); box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
    
    <?php echo $message; ?>

    <div style="background: rgba(255,255,255,0.8); padding: 20px; margin-bottom: 20px; border-radius: 20px; border: 1px solid #eee;">
        <h3 style="margin-top:0;">Add New <?php echo $isSysAdmin ? 'Admin/Guard' : 'Guard'; ?></h3>
        <form method="post" class="form-inline">
            <input type="text" name="admin_id" placeholder="ID Number" required>
            <input type="text" name="fname" placeholder="First Name" required>
            <input type="text" name="lname" placeholder="Last Name" required>
            <input type="password" name="password" placeholder="Password" required>
            
            <?php if($isSysAdmin): ?>
                <select name="role" required>
                    <option value="System Admin">System Admin (Lvl 3)</option>
                    <option value="Security Admin">Security Admin (Lvl 2)</option>
                    <option value="Guard">Guard (Lvl 1)</option>
                </select>
            <?php else: ?>
                <input type="text" value="Guard (Lvl 1)" readonly style="background:#eee; width: 120px;">
            <?php endif; ?>
            
            <button type="submit" name="btnAddAdmin" class="btn-enter" style="padding: 8px 20px; margin: 0; font-size: 16px;">Add</button>
        </form>
    </div>

    <h3 style="margin-top: 0;">Directory</h3>
    <div style="overflow-x: auto; border-radius: 15px; border: 1px solid #eee;">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Lvl</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['last_name'] . ', ' . $row['first_name']); ?></td>
                    
                    <form method="post" style="margin: 0;">
                        <input type="hidden" name="update_id" value="<?php echo htmlspecialchars($row['user_id']); ?>">
                        
                        <?php if($isSysAdmin): ?>
                            <td>
                                <select name="role" style="padding: 5px; border-radius: 10px;">
                                    <option value="System Admin" <?php if($row['admin_role'] == 'System Admin') echo 'selected'; ?>>System Admin</option>
                                    <option value="Security Admin" <?php if($row['admin_role'] == 'Security Admin') echo 'selected'; ?>>Security Admin</option>
                                    <option value="Guard" <?php if($row['admin_role'] == 'Guard') echo 'selected'; ?>>Guard</option>
                                </select>
                            </td>
                            <td><span style="background: #eee; padding: 5px 10px; border-radius: 15px; font-size: 14px;"><?php echo htmlspecialchars($row['access_level']); ?></span></td>
                        <?php else: ?>
                            <td><span style="background: #eee; padding: 5px 10px; border-radius: 15px; font-size: 14px;"><?php echo htmlspecialchars($row['admin_role']); ?></span></td>
                            <td><span style="background: #eee; padding: 5px 10px; border-radius: 15px; font-size: 14px;"><?php echo htmlspecialchars($row['access_level']); ?></span></td>
                        <?php endif; ?>
                        
                        <td style="display: flex; gap: 5px;">
                            <?php if($isSysAdmin): ?><button type="submit" name="btnUpdateAdmin" class="btn-small btn-edit">Update</button><?php endif; ?>
                    </form>
                    
                    <form method="post" style="margin: 0;" onsubmit="return confirm('Delete this account?');">
                            <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($row['user_id']); ?>">
                            <button type="submit" name="btnDeleteAdmin" class="btn-small btn-delete">Delete</button>
                    </form>
                        </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>