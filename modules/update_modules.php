<?php
// error_reporting(0);
header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include('function_modules.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == 'PUT') {

    $inputData = json_decode(file_get_contents("php://input"),true);
    if(empty($inputData)) {
        $storeModule = updateModule($_POST,$_GET);
    } else {
        $storeModule = updateModule($inputData,$_GET);
    }
    echo $storeModule;
}else {
    $data = [
        'status' => 405,
        'message' => $requestMethod. ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}


?>