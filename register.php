// example only

<?php    
include 'connect.php';    
require_once 'includes/header.php';
?>
 
<div style='background-color:#ffff00'>
    <center>
        <h2>User Registration Page</h2>
    </center>
</div>  
 
    <div>
        <form method="post">
        <pre>
            Firstname: <input type="text" name="txtfirstname">
            Lastname:  <input type="text" name="txtlastname">
 
            Program:
            <select name="txtprogram">
                <option value="">----</option>
                <option value="BSCS">BSCS</option>
                <option value="BSIT">BSIT</option>
            </select>
 
            Year Level:
            <select name="txtyearlevel">
                <option value="">----</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
 
            Gender:
            <select name="txtgender">
                <option value="">----</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
 
            Email:    <input type="text" name="txtemail">
            Password: <input type="password" name="txtpassword">
 
            <input type="submit" name="btnRegister" value="Register">
        </pre>
        </form>
    </div>
 
    <?php
    if(isset($_POST['btnRegister'])) {
 
        $fname = $_POST['txtfirstname'];
        $lname = $_POST['txtlastname'];
        $program = $_POST['txtprogram'];
        $yearlevel = $_POST['txtyearlevel'];
        $email = $_POST['txtemail'];
        $password = password_hash($_POST['txtpassword'], PASSWORD_DEFAULT);
        $gender = $_POST['txtgender'];
 
       
        $sql1 = "INSERT INTO tblstudent(firstname,lastname,program,yearlevel)
                VALUES('$fname','$lname','$program','$yearlevel')";
        mysqli_query($connection, $sql1);
 
        $sid = mysqli_insert_id($connection);
 
        $sql2 = "INSERT INTO tbluserprofile(gender,sid)
                VALUES('$gender','$sid')";
        mysqli_query($connection, $sql2);
 
        $sql3 = "INSERT INTO tbluseraccount(email,password,sid)
                VALUES('$email','$password','$sid')";
        mysqli_query($connection, $sql3);
 
        header("location: dashboard.php");
        exit();
    }
    ?>
 
<?php require_once 'includes/footer.php'; ?>
 