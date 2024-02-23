<?php

header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include('function_exercise.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == 'GET'){

    if(isset($_GET['exe_num'])){
 
    $getModuleExercise = getModuleExercise($_GET);
    echo $getModuleExercise;
        
    }else{
        
  $getModuleExerciseList = getModuleExerciseList();
  echo $getModuleExerciseList;
    }

}else{
$data = [
    'status' => 405,
    'message' => $requestMethod. 'Method Not Allowed',
];
header("HTTP/1.0 405 Method Not Allowed");
echo json_encode($data);
}

?>