<?php
// error_reporting(0);
header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include('module_excercise.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == 'DELETE'){

$deletemoduleexcercise = deletemoduleexcercise($_GET);
echo $deletemoduleexcercise;

}else{
$data = [
    'status' => 405,
    'messag' => $requestMethod. 'Method Not Allowed',
];
header("HTTP/1.0 405 Method Not Allowed");
echo json_encode($data);
}
?>