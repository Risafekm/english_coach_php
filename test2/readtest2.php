<?php
error_reporting(0);
header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include('functiontest2.php');


$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == 'GET'){

    if(isset($_GET['prelim_trans_ques_num'])){
 
    $preliminaryTest2 = getPreliminaryTest2($_GET);
    echo $preliminaryTest2;
        
    }else{
        
  $preliminaryTest2List = getPreliminaryTest2List();
  echo $preliminaryTest2List;
    }

}else{
$data = [
    'status' => 405,
    'message' => $requestMethod. 'Method Not Allowed',
];
header("HTTP/1.0 405 Method Not Allowed");
echo json_encode($data);
}