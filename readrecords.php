<?php
   
    include 'connect.php';
   
    if (!$connection) {
        die('Could not connect: ' . mysqli_connect_error());
}
   
    $query = 'SELECT * from  tblstudent';
    $resultset = mysqli_query($connection, $query);
   
    //$querybsit = 'SELECT count(*) as total from  tblstudent where program = "BSIT"';
    //$resultset1 = mysqli_query($connection, $querybsit);
    //$count = mysqli_fetch_assoc($resultset1);
 
    $sql_profiles = "SELECT
                    s.id,
                    s.firstname,
                    s.lastname,
                    s.program,
                    s.yearlevel,
                    p.gender,
                    u.email
                FROM tblstudent s
                JOIN tbluserprofile p ON s.id = p.sid
                JOIN tbluseraccount u ON s.id = u.sid";
 
    $result_profiles = mysqli_query($connection, $sql_profiles);
       
   
?>