<?php
// error_reporting(0);
header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include('function_final_test.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == 'DELETE'){

$deleteFinalTest = deleteFinalTest($_GET);
echo $deleteFinalTest;

}else{
$data = [
    'status' => 405,
    'message' => $requestMethod. 'Method Not Allowed',
];
header("HTTP/1.0 405 Method Not Allowed");
echo json_encode($data);
}