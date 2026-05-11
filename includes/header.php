<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $base_url ?? ''; ?>css/site.css"/>

    <title>Campus Access Control - <?php echo $title ?? 'Home'; ?></title>
</head>
<body>
<div class="wrapper">

<!-- Sidebar -->
<nav id="sidebar">
    <div class="sidebar-header text-center p-3">
        <img src="<?php echo $base_url ?? ''; ?>images/logosrp.png" width="150" height="50" alt="Logo">
        <h6 class="mt-2 text-white">Campus Access Control</h6>
    </div>

    <ul class="list-unstyled components px-3">
        <li>
            <a href="<?php echo $base_url ?? ''; ?>dashboard.php">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
        </li>

        <li>
            <a href="#adminMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-user-shield mr-2"></i> Admin
            </a>
            <ul class="collapse list-unstyled" id="adminMenu">
                <li><a href="<?php echo $base_url ?? ''; ?>admin/list.php">Manage Admins</a></li>
                <li><a href="<?php echo $base_url ?? ''; ?>admin/add.php">Add Admin</a></li>
            </ul>
        </li>

        <li>
            <a href="#studentMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-user-graduate mr-2"></i> Students
            </a>
            <ul class="collapse list-unstyled" id="studentMenu">
                <li><a href="<?php echo $base_url ?? ''; ?>student/list.php">Manage Students</a></li>
                <li><a href="<?php echo $base_url ?? ''; ?>student/add.php">Add Student</a></li>
            </ul>
        </li>

        <li>
            <a href="#personnelMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-user-tie mr-2"></i> Personnel
            </a>
            <ul class="collapse list-unstyled" id="personnelMenu">
                <li><a href="<?php echo $base_url ?? ''; ?>personnel/list.php">Manage Personnel</a></li>
                <li><a href="<?php echo $base_url ?? ''; ?>personnel/add.php">Add Personnel</a></li>
            </ul>
        </li>

        <li>
            <a href="#visitorMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-user-friends mr-2"></i> Visitors
            </a>
            <ul class="collapse list-unstyled" id="visitorMenu">
                <li><a href="<?php echo $base_url ?? ''; ?>visitor/list.php">Manage Visitors</a></li>
                <li><a href="<?php echo $base_url ?? ''; ?>visitor/add.php">Add Visitor</a></li>
            </ul>
        </li>

        <li>
            <a href="<?php echo $base_url ?? ''; ?>visit/list.php">
                <i class="fas fa-clipboard-list mr-2"></i> Visit Records
            </a>
        </li>

        <li>
            <a href="<?php echo $base_url ?? ''; ?>entry/list.php">
                <i class="fas fa-door-open mr-2"></i> Entry Records
            </a>
        </li>

        <li class="mt-3">
            <a href="<?php echo $base_url ?? ''; ?>logout.php" class="text-danger">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </li>
    </ul>
</nav>

<!-- Page Content -->
<div id="content">
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
        <button type="button" id="sidebarCollapse" class="btn btn-maroon">
            <i class="fas fa-bars"></i>
        </button>
        <span class="navbar-brand ml-3 font-weight-bold text-maroon">
            <?php echo $title ?? 'Dashboard'; ?>
        </span>
        <div class="ml-auto pr-3">
            <span class="text-muted small">
                <i class="fas fa-user-circle mr-1"></i>
                <?php echo isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Admin'; ?>
            </span>
        </div>
    </nav>

    <div class="container-fluid px-4">