<?php
session_start();
include 'connect.php';

$message = "";
$messageType = ""; 
$lastScan = null; 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnEnter'])) {
    $type = $_POST['user_type'];
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $now = date('Y-m-d H:i:s');
    $today = date('Y-m-d'); 
    
    if($type == 'Visitor') {
        $uid = 'V-' . date('Y-m-d-H:i:s'); 
    } else {
        $uid = trim($_POST['id_num']);
    }
    
    if (empty($uid)) {
        $message = "Error: ID Number is required.";
        $messageType = "error";
    } else {
        // Base User Table (Password is NULL for kiosk entries)
        $checkUser = mysqli_query($connection, "SELECT * FROM tbluser WHERE user_id = '$uid'");
        if(mysqli_num_rows($checkUser) == 0) {
            mysqli_query($connection, "INSERT INTO tbluser (user_id, first_name, last_name, user_type, password) VALUES ('$uid', '$fname', '$lname', '$type', NULL)");
        }

        // Subtypes
        if($type == 'Visitor') {
            $contact = $_POST['contact'] ?? '';
            mysqli_query($connection, "INSERT INTO tblvisitor (user_id, contact_number) VALUES ('$uid', '$contact')");
        } elseif ($type == 'Student') {
            $checkStu = mysqli_query($connection, "SELECT * FROM tblstudent WHERE user_id = '$uid'");
            if(mysqli_num_rows($checkStu) == 0) {
                $cy = $_POST['course_yr'] ?? 'N/A';
                mysqli_query($connection, "INSERT INTO tblstudent (user_id, course, year_level, status) VALUES ('$uid', '$cy', 1, 'Active')");
            }
        } elseif ($type == 'Personnel') {
            $checkPer = mysqli_query($connection, "SELECT * FROM tblpersonnel WHERE user_id = '$uid'");
            if(mysqli_num_rows($checkPer) == 0) {
                $role = $_POST['role'] ?? 'Staff';
                $dept = $_POST['dept'] ?? 'General';
                mysqli_query($connection, "INSERT INTO tblpersonnel (user_id, role, department, employment_status) VALUES ('$uid', '$role', '$dept', 'Active')");
            }
        }
        
        // Visitor Purpose
        if($type == 'Visitor') {
             $purpose = $_POST['purpose'] ?? 'General Visit';
             mysqli_query($connection, "INSERT INTO tblvisit (visitor_id, visit_date, purpose) VALUES ('$uid', '$today', '$purpose')");
        }
        
        // Logging
        $openEntry = mysqli_query($connection, "SELECT entry_id FROM tblentry_record WHERE user_id = '$uid' AND exit_time IS NULL ORDER BY entry_time DESC LIMIT 1");
        
        if(mysqli_num_rows($openEntry) > 0) {
            $row = mysqli_fetch_assoc($openEntry);
            $entry_id = $row['entry_id'];
            mysqli_query($connection, "UPDATE tblentry_record SET exit_time = '$now' WHERE entry_id = '$entry_id'");
        } else {
            mysqli_query($connection, "INSERT INTO tblentry_record (user_id, entry_time) VALUES ('$uid', '$now')");
        }
        
        $lastScan = [
            'id' => $uid,
            'fname' => $fname,
            'lname' => $lname,
            'type' => $type,
            'time' => $now
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Access Control - Main Gate</title>
    <link rel="stylesheet" href="css/site.css">
    <style>
        .form-group select {
            flex: 1; padding: 12px 15px; border: none; border-radius: 30px;
            font-size: 16px; box-shadow: inset 0 2px 5px rgba(0,0,0,0.1); background: #fff;
        }
    </style>
</head>
<body>

<header class="main-header">
    <h1>Campus Access Control - Main Gate</h1>
    <div class="datetime">
        <span class="live-date"></span><br><span class="live-time"></span>
    </div>
</header>

<div class="tabs">
    <div class="tab active" onclick="switchTab(this, 'student')">Student</div>
    <div class="tab" onclick="switchTab(this, 'personnel')">Personnel</div>
    <div class="tab" onclick="switchTab(this, 'visitor')">Visitor</div>
</div>

<div class="glass-container">
    
    <div class="form-section">
        <form id="form-student" method="post" action="kiosk.php">
            <input type="hidden" name="user_type" value="Student">
            <div class="form-group"><label>First Name:</label> <input type="text" name="fname" required></div>
            <div class="required-lbl">*required</div>
            <div class="form-group"><label>Last Name:</label> <input type="text" name="lname" required></div>
            <div class="required-lbl">*required</div>
            <div class="form-group"><label>ID:</label> <input type="text" name="id_num" required></div>
            <div class="required-lbl">*required</div>
            <div class="form-group"><label>Course & Year:</label> <input type="text" name="course_yr"></div>
            <div class="required-lbl">*required</div>
            <button type="submit" name="btnEnter" class="btn-enter">Enter</button>
        </form>

        <form id="form-personnel" style="display:none;" method="post" action="kiosk.php">
            <input type="hidden" name="user_type" value="Personnel">
            <div class="form-group"><label>First Name:</label> <input type="text" name="fname" required></div>
            <div class="required-lbl">*required</div>
            <div class="form-group"><label>Last Name:</label> <input type="text" name="lname" required></div>
            <div class="required-lbl">*required</div>
            <div class="form-group"><label>ID:</label> <input type="text" name="id_num" required></div>
            <div class="required-lbl">*required</div>
            <div class="form-group">
                <label>Role:</label> 
                <select name="role" required>
                    <option value="Faculty">Faculty</option>
                    <option value="Staff">Staff</option>
                    <option value="Security">Security</option>
                </select>
            </div>
            <div class="form-group"><label>Department:</label> <input type="text" name="dept"></div>
            <button type="submit" name="btnEnter" class="btn-enter">Enter</button>
        </form>

        <form id="form-visitor" style="display:none;" method="post" action="kiosk.php">
            <input type="hidden" name="user_type" value="Visitor">
            <div class="form-group"><label>First Name:</label> <input type="text" name="fname" required></div>
            <div class="required-lbl">*required</div>
            <div class="form-group"><label>Last Name:</label> <input type="text" name="lname" required></div>
            <div class="required-lbl">*required</div>
            <div class="form-group"><label>Contact No:</label> <input type="text" name="contact"></div>
            <div class="form-group"><label>Purpose:</label> <input type="text" name="purpose" required></div>
            <div class="required-lbl">*required</div>
            
            <button type="submit" name="btnEnter" class="btn-enter" style="background: linear-gradient(to bottom, #d28c46, #8b5120);">Print Pass</button>
        </form>
    </div>

    <div class="preview-card" style="align-self: flex-start; margin-top: 10px;">
        <div class="preview-top">
            <div class="avatar-placeholder" style="font-size:30px;">👤</div>
            <div style="font-weight: bold; font-size: 16px;">
                <?php echo $lastScan ? ($lastScan['type'] == 'Visitor' ? 'Pass: ' : 'ID: ') . htmlspecialchars($lastScan['id']) : 'ID: ---'; ?>
            </div>
        </div>  
        <p>Date: <span style="font-weight: normal;"><?php echo $lastScan ? date('m/d/Y', strtotime($lastScan['time'])) : '<span class="live-date"></span>'; ?></span></p>
        <p>Time: <span style="font-weight: normal;"><?php echo $lastScan ? date('H:i:s', strtotime($lastScan['time'])) : '<span class="live-time"></span>'; ?></span></p>
        <p>Last Name: <span style="font-weight: normal;"><?php echo $lastScan ? htmlspecialchars($lastScan['lname']) : '---'; ?></span></p>
        <p>First Name: <span style="font-weight: normal;"><?php echo $lastScan ? htmlspecialchars($lastScan['fname']) : '---'; ?></span></p>
        <p>Person Type: <span style="font-weight: normal;"><?php echo $lastScan ? htmlspecialchars($lastScan['type']) : '---'; ?></span></p>
        
        <?php if($lastScan && $lastScan['type'] == 'Visitor'): ?>
            <p style="margin-top: 20px; color: green; font-weight:bold; font-size: 12px; text-align: center;">Visitor Pass Generated Successfully. Printing...</p>
        <?php endif; ?>
    </div>

</div>

<?php if(isset($_SESSION['admin_id'])): ?>
<a href="dashboard.php" style="position: absolute; bottom: 20px; right: 20px; color: white; background: rgba(0,0,0,0.5); padding: 10px; border-radius: 10px; text-decoration: none;">Return to Guard Dashboard</a>
<?php endif; ?>

<script>
    function updateClock() {
        const now = new Date();
        document.querySelectorAll('.live-date').forEach(el => el.innerText = now.toLocaleDateString('en-US'));
        document.querySelectorAll('.live-time').forEach(el => el.innerText = now.toLocaleTimeString('en-GB')); 
    }
    setInterval(updateClock, 1000); updateClock();

    function switchTab(element, tabId) {
        document.getElementById('form-student').style.display = 'none';
        document.getElementById('form-personnel').style.display = 'none';
        document.getElementById('form-visitor').style.display = 'none';
        
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        
        document.getElementById('form-' + tabId).style.display = 'block';
        element.classList.add('active');
    }
</script>
</body>
</html>