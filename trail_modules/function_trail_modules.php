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

// Function to fetch table count from the database
function getCount() {
    global $conn;
    try {
        // Perform the SQL query to fetch the count of rows in the table
        $query = "SELECT COUNT(mod_num) as count FROM edu_modules_trial";
        
        // Execute the query
        $result = $conn->query($query);

        // Check if query was successful
        if ($result) {
            // Fetch data and store it in an array
            $row = $result->fetch_assoc();
            if (isset($row['count'])) {
                // Access the "count" key if it exists
                $tableCount = $row['count'];
            }
            // Return the table count
            return $tableCount;
        }  else {
            // Handle query error
            throw new Exception("Failed to execute query: $query");
        }
    } catch (PDOException $e) {
        // Handle PDOException (SQLite errors)
        error_log("SQLite Error: " . $e->getMessage());
        return false;
    }

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
    $mod_name = mysqli_real_escape_string($conn, $TrailModulesInput['mod_name']);
    $mod_content = mysqli_real_escape_string($conn, $TrailModulesInput['mod_content']);
    $mod_description = mysqli_real_escape_string($conn, $TrailModulesInput['mod_description']);
    $mod_specialnote = mysqli_real_escape_string($conn, $TrailModulesInput['mod_specialnote']);
    $sl_level = mysqli_real_escape_string($conn, $TrailModulesInput['sl_level']);
    $mod_example_explanation = mysqli_real_escape_string($conn, $TrailModulesInput['mod_example_explanation']);


    if(empty(trim($mod_order))){
     return error422('Enter the mod order');
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
    }
    else{

       $query = "INSERT INTO `edu_modules_trial`(`mod_num`, `mod_order`, `t_num`, `mod_name`, `mod_content`, `mod_description`, `mod_specialnote`, `sl_level`, `mod_example_explanation`) VALUES ('$mod_num','$mod_order','0','$mod_name','$mod_content','$mod_description','$mod_specialnote','$sl_level','0')";
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

    }elseif(empty(trim($modName))){
        return error422('enter name');

    }elseif(empty(trim($modContent))){

        return error422('enter content');
    } elseif(empty(trim($modDescription))){
        return error422('enter description');
    }
    else {

        $query = "UPDATE `edu_modules_trial` SET `mod_num` = '$modNum', `mod_order` ='$modOrder', `mod_name` = '$modName', `mod_content` ='$modContent',`mod_description`='$modDescription' WHERE `mod_num` = $modNum LIMIT 1";

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