<?php

require 'connection.php';

//error message 

function error422($message){
    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 Unprocessable Entity");
    echo json_encode($data);
    exit();
}

//update
function updateModule($moduleInput,$moduleParams){

    global $conn;

    if(!isset($moduleParams['mod_num'])) {
        return error422('module number is not found in url');
    } elseif($moduleParams['mod_num']== null){
        return error422('Enter the number');
    }

    $modNum = mysqli_real_escape_string($conn,$moduleParams['mod_num']);
    $modOrder = mysqli_real_escape_string($conn,$moduleInput['mod_order']);
    $tNum = mysqli_real_escape_string($conn,$moduleInput['t_num']);
    $modName = mysqli_real_escape_string($conn,$moduleInput['mod_name']);
    $modContent = mysqli_real_escape_string($conn,$moduleInput['mod_content']);
    $modDescription = mysqli_real_escape_string($conn,$moduleInput['mod_description']);


    if(empty(trim($modNum))){

        return error422('enter number');

    }elseif(empty(trim($modOrder))){
        return error422('enter order');

    }elseif(empty(trim($tNum))){
        return error422('enter number');

    }elseif(empty(trim($modName))){
        return error422('enter name');

    }elseif(empty(trim($modContent))){

        return error422('enter content');
    } elseif(empty(trim($modDescription))){
        return error422('enter description');
    }
    else {

        $query = "UPDATE `edu_modules` SET `mod_num` = '$modNum', `mod_order` ='$modOrder', `t_num` = '$tNum', `mod_name` = '$modName', `mod_content` ='$modContent',`mod_description`='$modDescription' WHERE `mod_num` = $modNum LIMIT 1";

        $result = mysqli_query($conn, $query);

        if($result){
  
            $data = [
                'status' => 200,
                'message' => 'modules updated Successfully',
            ];
            header("HTTP/1.0 200 Created");
            echo json_encode($data);
             
    
         }else{
            $data = [
                'status' => 500,
                'message' => 'Internal Server Error',
            ];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data);
         }
    
        }
    }
 
    //delete
    function deleteModule($moduleParams) {

        global $conn;

        if(!isset($moduleParams['mod_num'])){
            return error422('module number is not found in URL');
        }elseif($moduleParams['mod_num']== null) {
            return error422('Enter the module number');
        }
        $moduleNum = mysqli_real_escape_string($conn,$moduleParams['mod_num']);

        $query = "DELETE FROM `edu_modules` WHERE `mod_num`=$moduleNum";
        $result = mysqli_query($conn,$query);

        if($result) {
            $data = [
                'status' => 200,
                'message' => 'modules deleted Successfully',
            ];
            header("HTTP/1.0 200 ok");
            return json_encode($data);

        } else {
            $data = [
                'status' => 404,
                'message' => 'module not found',
            ];
            header("HTTP/1.0 404  not found");
            return json_encode($data);
        }

    }
//post

function storeModules($moduleInput) {
    global $conn;

    $modNum = mysqli_real_escape_string($conn,$moduleInput['mod_num']);
    $modOrder = mysqli_real_escape_string($conn,$moduleInput['mod_order']);
    $tNum = mysqli_real_escape_string($conn,$moduleInput['t_num']);
    $modName = mysqli_real_escape_string($conn,$moduleInput['mod_name']);
    $modContent = mysqli_real_escape_string($conn,$moduleInput['mod_content']);
    $modDescription = mysqli_real_escape_string($conn,$moduleInput['mod_description']);


    if(empty(trim($modOrder))){

        return error422('enter mod order');

    }elseif(empty(trim($modName))){
        return error422('enter mod name');

    }elseif(empty(trim($modContent))){

        return error422('enter mod content');
    } elseif(empty(trim($modDescription))){
        return error422('enter mod description');
    }
    else {
        $query = "INSERT INTO `edu_modules`(`mod_num`,`mod_order`,`t_num`,`mod_name`,`mod_content`,`mod_description`) VALUES('$modNum','$modOrder','$tNum','$modName','$modContent','$modDescription')";
        $result = mysqli_query($conn,$query);

        if($result){

            $data = [
                'status' => 201,
                'message' => 'modules created successfully',
            ];
            header("HTTP/1.0 201  created");
            return json_encode($data);

        }else {
            $data = [
                'status' => 500,
                'message' => 'Internal Server Error',
            ];
            header("HTTP/1.0 500  Internal Server Error");
            return json_encode($data);
        }
    }


}


//getFunction

function getModulesList() {
global $conn;
$query = "SELECT * FROM edu_modules";
$query_run = mysqli_query($conn ,$query);

if($query_run){

if(mysqli_num_rows($query_run) > 0){

$res = mysqli_fetch_all($query_run,MYSQLI_ASSOC);

  $data = $res;
    
header("HTTP/1.0 200  Success");
return json_encode($data);

}else{
    $data = [
        'status' => 404,
        'message' => 'No Student Found',
    ];
    header("HTTP/1.0 404  No Student Found");
    return json_encode($data);
}

}else{
    $data = [
        'status' => 500,
        'message' => 'Internal Server Error',
    ];
    header("HTTP/1.0 500  Internal Server Error");
    return json_encode($data);
}

}

//1 to 1 data fetching

function getModules($getModulesparams){

global $conn;


if($getModulesparams['mod_num'] == null){
    return error422('Enter your id');
   }

$modNum = mysqli_real_escape_string($conn, $getModulesparams['mod_num']);

$query = "SELECT * FROM edu_modules WHERE mod_num = '$modNum' LIMIT 1";
$result = mysqli_query($conn,$query);

if($result){

 if(mysqli_num_rows($result) > 0){
 
    $res = mysqli_fetch_assoc($result);
    $data =$res;
    header("HTTP/1.0 200  Success");
    return json_encode($data);


 }else{
 $data = [
        'status' => 404,
        'message' => 'No edu_preliminary_trans_questions Found',
    ];
    header("HTTP/1.0 404  Not found");
    return json_encode($data);
 }

}else{
    $data = [
        'status' => 500,
        'message' => 'Internal Server Error',
    ];
    header("HTTP/1.0 500  Internal Server Error");
    return json_encode($data);
}

}

?>