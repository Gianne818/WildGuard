<?php
session_start();
include 'connect.php';
if(!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_role'])) { header("Location: index.php"); exit(); }

// Fetch the Most Recent Entry for the Center Card (TODAY ONLY)
$latestQuery = "SELECT e.*, u.first_name, u.last_name, u.user_type 
                FROM tblentry_record e 
                JOIN tbluser u ON e.user_id = u.user_id 
                WHERE DATE(e.entry_time) = CURDATE()
                ORDER BY e.entry_time DESC LIMIT 1";
$latestResult = mysqli_query($connection, $latestQuery);
$latest = mysqli_fetch_assoc($latestResult);

// Fetch Active Visitors Count (TODAY ONLY)
$activeVisQuery = mysqli_query($connection, "SELECT COUNT(*) as active_count FROM tblentry_record e JOIN tbluser u ON e.user_id = u.user_id WHERE u.user_type = 'Visitor' AND e.exit_time IS NULL AND DATE(e.entry_time) = CURDATE()");
$activeVis = mysqli_fetch_assoc($activeVisQuery)['active_count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Guard Dashboard</title>
    <link rel="stylesheet" href="css/site.css">
</head>
<body>

<header class="main-header">
    <div style="display:flex; align-items:center; gap:0px;">
        <div style="width:40px; height:40px; background:#fff; border-radius:50%; display:flex; justify-content:center; align-items:center; font-size:24px; color:#ccc;">👤</div>
        <span style="padding-left:10px;"><?php echo htmlspecialchars($_SESSION['admin_name']); ?> (<?php echo htmlspecialchars($_SESSION['admin_role']); ?>)</span>
        
        <a href="kiosk.php" target="_blank" style="margin-left:20px; background:rgba(255,255,255,0.2); color:#fff; padding:5px 15px; border-radius:15px; text-decoration:none; font-size:14px; border: 1px solid #fff;">Launch Kiosk</a>
        
        <?php if($_SESSION['admin_role'] === 'System Admin'): ?>
            <a href="manage_admins.php" style="margin-left:5px; background:rgba(255,255,255,0.2); color:#fff; padding:5px 15px; border-radius:15px; text-decoration:none; font-size:14px; border: 1px solid #fff;">Manage Admins</a>
        <?php elseif($_SESSION['admin_role'] === 'Security Admin'): ?>
            <a href="manage_admins.php" style="margin-left:5px; background:rgba(255,255,255,0.2); color:#fff; padding:5px 15px; border-radius:15px; text-decoration:none; font-size:14px; border: 1px solid #fff;">Manage Guards</a>
        <?php endif; ?>
        
        <?php if($_SESSION['admin_role'] === 'System Admin' || $_SESSION['admin_role'] === 'Security Admin'): ?>
            <a href="all_records.php" style="margin-left:5px; background:rgba(255,255,255,0.2); color:#fff; padding:5px 15px; border-radius:15px; text-decoration:none; font-size:14px; border: 1px solid #fff;">All Records</a>
        <?php endif; ?>
        
        <?php if($_SESSION['admin_role'] !== 'Guard'): ?>
            <a href="manage_users.php" style="margin-left:5px; background:rgba(255,255,255,0.2); color:#fff; padding:5px 15px; border-radius:15px; text-decoration:none; font-size:14px; border: 1px solid #fff;">Manage Users</a>
        <?php endif; ?>
        
        <a href="logout.php" style="margin-left:5px; background:#cc0000; color:#fff; padding:5px 15px; border-radius:15px; text-decoration:none; font-size:14px; border: 1px solid #fff;">Logout</a>
    </div>
    <h1>WILD GUARD</h1>
    <div class="datetime">
        <span class="live-date"></span><br><span class="live-time"></span>
    </div>
</header>

<div class="glass-container" style="flex-direction: column; padding: 20px;">
    
    <div class="dash-grid">
        <div class="dash-panel" style="display: flex; flex-direction: column; align-items: center;">
            <h2 style="text-align:center; margin-top:0;">Active Visitors Today:<br><span style="font-size: 32px;"><?php echo $activeVis; ?></span></h2>
            <div style="width: 100%; font-size:12px; margin-top:10px; flex: 1; max-height: 340px; overflow-y: auto; background: rgba(255,255,255,0.5); padding: 10px; border-radius: 8px;">
                <?php
                // Fetch sidebar logs for TODAY ONLY as a flat IN/OUT timeline
                $sidebarLogs = mysqli_query($connection, "SELECT e.entry_id, e.user_id, 'IN' AS event_type, e.entry_time AS event_time FROM tblentry_record e JOIN tbluser u ON e.user_id = u.user_id WHERE DATE(e.entry_time) = CURDATE() UNION ALL SELECT e.entry_id, e.user_id, 'OUT' AS event_type, e.exit_time AS event_time FROM tblentry_record e JOIN tbluser u ON e.user_id = u.user_id WHERE DATE(e.entry_time) = CURDATE() AND e.exit_time IS NOT NULL ORDER BY event_time DESC, event_type DESC LIMIT 15");
                while($sLog = mysqli_fetch_assoc($sidebarLogs)): 
                    $isOut = $sLog['event_type'] === 'OUT';
                ?>
                <p style="margin: 5px 0; border-bottom: 1px solid #ddd; padding-bottom: 3px;">
                    <?php echo htmlspecialchars($sLog['user_id']); ?> 
                    <span style="float:right; color:<?php echo $isOut ? '#cc0000' : '#009900'; ?>; font-weight:bold;">
                        <?php echo htmlspecialchars($sLog['event_type']); ?>
                    </span>
                    <br>
                    <small style="color:#666;"><?php echo date('H:i:s', strtotime($sLog['event_time'])); ?></small>
                </p>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="dash-panel dash-center" style="flex-direction: row; gap: 40px; justify-content: center; background: rgba(255,255,255,0.6);">
            <?php if($latest): ?>
                <div class="preview-card" style="box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: 1px solid #eee;">
                    <div class="preview-top">
                        <div class="avatar-placeholder" style="font-size:30px;">👤</div>
                        <div style="font-weight: bold; font-size: 16px;">ID: <?php echo htmlspecialchars($latest['user_id']); ?></div>
                    </div>
                    <p>Date: <span style="font-weight: normal;"><?php echo date('m/d/Y', strtotime($latest['entry_time'])); ?></span></p>
                    <p>Time: <span style="font-weight: normal;"><?php echo date('H:i:s', strtotime($latest['entry_time'])); ?></span></p>
                    <p>Last Name: <span style="font-weight: normal; text-transform: uppercase;"><?php echo htmlspecialchars($latest['last_name']); ?></span></p>
                    <p>First Name: <span style="font-weight: normal; text-transform: uppercase;"><?php echo htmlspecialchars($latest['first_name']); ?></span></p>
                    <p>Person Type: <span style="font-weight: normal; text-transform: uppercase;"><?php echo htmlspecialchars($latest['user_type']); ?></span></p>
                </div>
                
                <div style="text-align:center;">
                    <div style="background:#00cc00; width:150px; height:150px; border-radius:50%; display:flex; justify-content:center; align-items:center; margin: 0 auto 20px auto; box-shadow: 0 10px 20px rgba(0,200,0,0.3);">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <h2 style="margin:0; font-size: 24px; letter-spacing: 1px;">ENTRY ACCEPTED</h2>
                </div>
            <?php else: ?>
                <h2>Waiting for scans today...</h2>
            <?php endif; ?>
        </div>

        <div class="dash-panel dash-recent" style="align-items: center; background: rgba(255,255,255,0.7);">
            <h3 style="margin:0 15px 0 0; font-size: 14px; text-transform: uppercase; color: #333;">Recent Entries Today:</h3>
            <?php
            // Fetch recent avatars for TODAY ONLY
            $recentAvatars = mysqli_query($connection, "SELECT e.*, u.first_name, u.last_name, u.user_type FROM tblentry_record e JOIN tbluser u ON e.user_id = u.user_id WHERE DATE(e.entry_time) = CURDATE() ORDER BY e.entry_time DESC LIMIT 6");
            while($rec = mysqli_fetch_assoc($recentAvatars)): 
            ?>
            <div class="recent-item" style="min-width: 200px;">
                <div style="width:45px; height:45px; background:#ddd; border-radius:5px; display:flex; justify-content:center; align-items:center; overflow: hidden;">
                    <img src="https://via.placeholder.com/45" alt="Avatar">
                </div>
                <div style="line-height: 1.2;">
                    <strong style="font-size: 14px;"><?php echo htmlspecialchars($rec['first_name'] . ' ' . $rec['last_name']); ?></strong><br>
                    <small style="color: #666; font-size: 11px;"><?php echo htmlspecialchars($rec['user_id'] . ' | ' . $rec['user_type']); ?></small>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

</div>

<script>
    function updateClock() {
        const now = new Date();
        document.querySelectorAll('.live-date').forEach(el => el.innerText = now.toLocaleDateString('en-US'));
        document.querySelectorAll('.live-time').forEach(el => el.innerText = now.toLocaleTimeString('en-GB')); 
    }
    setInterval(updateClock, 1000); updateClock();
    setTimeout(function(){ window.location.reload(); }, 5000);
</script>
</body>
</html>