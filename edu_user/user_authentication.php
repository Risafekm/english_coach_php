<?php
error_reporting(0);
header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include('edu_user.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == 'POST'){

$userParams = json_decode(file_get_contents("php://input"),true);
if(empty($userParams)){
   
    $getUser = getUser($_POST);
}else{

    $getUser = getUser($userParams);
}

echo $getUser;

}else{
$data = [
    'status' => 405,
    'message' => $requestMethod. ' Method Not Allowed',
];
header("HTTP/1.0 405 Method Not Allowed");
echo json_encode($data);
}
