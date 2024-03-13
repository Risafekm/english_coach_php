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

//get function

function getTrailModules(){

    global $conn;
    
        
    $query = "SELECT `mod_num`, `mod_order`, `t_num`, `mod_name`, `mod_content`, `mod_description`, `mod_specialnote`, `sl_level`, `mod_example_explanation` FROM `edu_modules_trial`";
    $result = mysqli_query($conn,$query);
    
    if($result){
    
    if(mysqli_num_rows($result) > 0){
     
    $res = mysqli_fetch_all($result,MYSQLI_ASSOC);

        $data =  $res;

header("HTTP/1.0 200  Success");
return json_encode($data);


}else{
    $data = [
     'status' => 404,
     'message' => 'No modules Found',
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

//postFunction

function storeTrailModules($TrailModulesInput){

    global $conn;

    $mod_order = mysqli_real_escape_string($conn, $TrailModulesInput['mod_order']);
    $t_num = mysqli_real_escape_string($conn, $TrailModulesInput['t_num']);
    $mod_name = mysqli_real_escape_string($conn, $TrailModulesInput['mod_name']);
    $mod_content = mysqli_real_escape_string($conn, $TrailModulesInput['mod_content']);
    $mod_description = mysqli_real_escape_string($conn, $TrailModulesInput['mod_description']);
    $mod_specialnote = mysqli_real_escape_string($conn, $TrailModulesInput['mod_specialnote']);
    $sl_level = mysqli_real_escape_string($conn, $TrailModulesInput['sl_level']);
    $mod_example_explanation = mysqli_real_escape_string($conn, $TrailModulesInput['mod_example_explanation']);


    if(empty(trim($mod_order))){
     return error422('Enter the mod order');
    }elseif(empty(trim(  $t_num))){
        return error422('Enter the t num');
    }elseif(empty(trim($mod_name))){
        return error422('Enter the mod name');
    }elseif(empty(trim( $mod_content))){
        return error422('Enter the mod content');
    }elseif(empty(trim($mod_description))){
        return error422('Enter the mod description');
    }elseif(empty(trim($mod_specialnote))){
        return error422('Enter the mod special note');
    }elseif(empty(trim($sl_level))){
        return error422('Enter the sl level');
    }elseif(empty(trim($mod_example_explanation))){
        return error422('Enter the mod_example_explanation');
    }
    else{

       $query = "INSERT INTO `edu_modules_trial`(`mod_num`, `mod_order`, `t_num`, `mod_name`, `mod_content`, `mod_description`, `mod_specialnote`, `sl_level`, `mod_example_explanation`) VALUES ('$mod_num','$mod_order','$t_num','$mod_name','$mod_content','$mod_description','$mod_specialnote','$sl_level','$mod_example_explanation')";
        $result = mysqli_query($conn, $query);

     if($result){
  
        $data = [
            'status' => 201,
            'message' => 'Trail Module List  Created Successfully',
        ];
        header("HTTP/1.0 201 Created");
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

//Update 

function updateTrailModules($TrailModuleInput, $TrailModuleParams)
{
    global $conn;

    if (!isset($TrailModuleParams['mod_num'])) {
        return error422('Trail module id not found in url');
    } elseif ($TrailModuleParams['mod_num'] == null) {
        return error422('Enter the Trail module id');
    }

    $mod_num = mysqli_real_escape_string($conn, $TrailModuleParams['mod_num']);
    $mod_order = mysqli_real_escape_string($conn, $TrailModuleInput['mod_order']);
    $t_num = mysqli_real_escape_string($conn, $TrailModuleInput['t_num']);
    $mod_name = mysqli_real_escape_string($conn, $TrailModuleInput['mod_name']);
    $mod_content = mysqli_real_escape_string($conn, $TrailModuleInput['mod_content']);
    $mod_description = mysqli_real_escape_string($conn, $TrailModuleInput['mod_description']);
    $mod_specialnote = mysqli_real_escape_string($conn, $TrailModuleInput['mod_specialnote']);
    $sl_level = mysqli_real_escape_string($conn, $TrailModuleInput['sl_level']);
    $mod_example_explanation = mysqli_real_escape_string($conn, $TrailModuleInput['mod_example_explanation']);

  

    if(empty(trim($mod_order))){
         return error422('Enter the mod order');
            }elseif(empty(trim(  $t_num))){
               return error422('Enter the t num');
           }elseif(empty(trim($mod_name))){
               return error422('Enter the mod name');
           }elseif(empty(trim( $mod_content))){
               return error422('Enter the mod content');
           }elseif(empty(trim($mod_description))){
               return error422('Enter the mod description');
           }elseif(empty(trim($mod_specialnote))){
               return error422('Enter the mod special note');
           }elseif(empty(trim($sl_level))){
               return error422('Enter the sl level');
           }elseif(empty(trim($mod_example_explanation))){
               return error422('Enter the mod_example_explanation');
           }
           else{

        $query = "UPDATE `edu_modules_trial` SET `mod_num`='$mod_num',`mod_order`='$mod_order',`t_num`='$t_num',`mod_name`='$mod_name',`mod_content`='$mod_content',`mod_description`='$mod_description',`mod_specialnote`='$mod_specialnote',`sl_level`='$sl_level',`mod_example_explanation`='$mod_example_explanation' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data = [
                'status' => 200,
                'message' => 'Trail Module Created Successfully',
            ];
            header("HTTP/1.0 200 Created");
            echo json_encode($data);

        } else {
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

function deleteTrailModules($TrailModulesParams){
 
    global $conn;

    if(!isset($TrailModulesParams['mod_num'])){
        return error422("Trial Module id not found in url ");
    }elseif($TrailModulesParams['mod_num'] == null){
    return error422("Enter the Trial Module id");
    }

    $mod_num = mysqli_real_escape_string($conn, $TrailModulesParams['mod_num']);
    $query = "DELETE FROM `edu_modules_trial` WHERE `mod_num` = $mod_num LIMIT 1";
    $result = mysqli_query($conn ,$query);

    if($result){

        $data = [
            'status' => 200,
            'message' => 'Trial Module deleted successfully',
        ];
        header("HTTP/1.0 200  success");
        return json_encode($data);

    }else{
        $data = [
            'status' => 404,
            'message' => 'Trial Module not found',
        ];
        header("HTTP/1.0 400  Not found");
        return json_encode($data);
    }


}






?>