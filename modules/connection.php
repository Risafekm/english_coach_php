<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "new_db";

$conn = mysqli_connect($host,$username,$password,$dbname);

if(!$conn) {
    die("connection failed:".mysqli_connect_error());
}
?>