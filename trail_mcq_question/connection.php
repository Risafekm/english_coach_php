<?php


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";
$conn = new mysqli($servername, $username, $password, $dbname);
// require_once('examples.php');
// require_once('modulesb.php');

//  require_once('exam.php');

// require_once('ex.php');

//   require_once('inserttests.php');

if($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>