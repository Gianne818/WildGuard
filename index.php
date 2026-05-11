<?php
session_start();
include 'connect.php';

if(isset($_POST['btnLogin'])) {
    $uid = $_POST['log_id'];
    $pass = $_POST['password'];
    
    // Join tbladmin to get the role
    $query = "SELECT u.*, a.admin_role FROM tbluser u JOIN tbladmin a ON u.user_id = a.admin_id WHERE u.user_id = '$uid' AND u.user_type = 'Admin'";
    $result = mysqli_query($connection, $query);
    
    if($row = mysqli_fetch_assoc($result)) {
        if(password_verify($pass, $row['password'])) {
            $_SESSION['admin_id'] = $row['user_id'];
            $_SESSION['admin_name'] = $row['first_name'] . ' ' . $row['last_name'];
            $_SESSION['admin_role'] = $row['admin_role']; // Store the role
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Incorrect Password');</script>";
        }
    } else {
        echo "<script>alert('Admin not found');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Access - Login</title>
    <link rel="stylesheet" href="css/site.css">
</head>
<body style="display: flex; justify-content: center; align-items: center;">

<div class="glass-container login-box" style="width: 400px; padding: 40px; flex-direction: column; align-items: stretch; background: rgba(255, 255, 255, 0.95); box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
    <h2 style="color:#6b2020; text-align: center; margin-top:0; font-size: 28px;">System Login</h2>
    <form method="post">
        <div class="form-group" style="flex-direction:column; align-items:flex-start;">
            <label style="width:100%; margin-bottom:10px; font-size: 16px;">Admin ID:</label> 
            <input type="text" name="log_id" required style="width:100%; box-sizing:border-box; margin-left:0; padding: 12px; background: #f0f0f0;">
        </div>
        <div class="form-group" style="flex-direction:column; align-items:flex-start; margin-top: 20px;">
            <label style="width:100%; margin-bottom:10px; font-size: 16px;">Password:</label> 
            <input type="password" name="password" required style="width:100%; box-sizing:border-box; margin-left:0; padding: 12px; background: #f0f0f0;">
        </div>
        <button type="submit" name="btnLogin" class="btn-enter" style="margin-left:0; width:100%; margin-top: 30px; font-size: 18px;">Login</button>
    </form>
</div>

</body>
</html>