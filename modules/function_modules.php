<?php

include 'connection.php';

function error422($message){
    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 unprocessable entity");
    echo json_encode($data);
    exit();
 }


function getModuleList() {
    global $conn;

    $query = "SELECT * FROM `edu_modules`";
    $query_run = mysqli_query($conn,$query);

    if($query_run){
        if(mysqli_num_rows($query_run) > 0){
        
            $res = mysqli_fetch_all($query_run,MYSQLI_ASSOC);

            // Return a JSON array of Customer objects
            $data = array();
            foreach ($res as $row) {
                $customer = array(
                    'mod_num' => $row['mod_num'],
                    'mod_order' => $row['mod_order'],
                    't_num' => $row['t_num'],
                    'mod_name' => $row['mod_name'],
                    'mod_content' => $row['mod_content'],
                    'mod_description' => $row['mod_description'],

                );
                array_push($data, $customer);
            }
            header("Content-Type: application/json");
            return json_encode($data);
            
        }else{
            $data = array(
                'status' => 404,
                'message' => 'No module Found',
            );
            header("HTTP/1.0 404  No module Found");
            return json_encode($data);
        }
        
    }else{
        $data = array(
            'status' => 500,
            'message' => 'Internal Server Error',
        );
        header("HTTP/1.0 500  Internal Server Error");
        return json_encode($data);
    }
}


function storeModule($moduleInput) {
    global $conn;

    $modNum = mysqli_real_escape_string($conn,$moduleInput['mod_num']);
    $modOrder = mysqli_real_escape_string($conn,$moduleInput['mod_order']);
    $tNum = mysqli_real_escape_string($conn,$moduleInput['t_num']);
    $modName = mysqli_real_escape_string($conn,$moduleInput['mod_name']);
    $modContent = mysqli_real_escape_string($conn,$moduleInput['mod_content']);
    $modDescription = mysqli_real_escape_string($conn,$moduleInput['mod_description']);


    if(empty(trim($modNum))){

        return error422('enter mod number');

    }elseif(empty(trim($modOrder))){
        return error422('enter mod order');

    }elseif(empty(trim($tNum))){
        return error422('enter t number');

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

?>