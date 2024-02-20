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

//delete

function deletemoduleexcercise($excerciseparams) {
    global $conn;

    if (!isset($excerciseparams['exe_num'])) {
        return error422("id not found ");
    }

    $exe_num = mysqli_real_escape_string($conn, $excerciseparams['exe_num']);

    // Start a transaction to ensure both queries are executed or none
    mysqli_begin_transaction($conn);

    // Delete from edu_module_exercises table
    $query1 = "DELETE FROM `edu_module_exercises` WHERE `exe_num` = $exe_num LIMIT 1";
    $result1 = mysqli_query($conn, $query1);


    if ($result1) {
        // Commit the transaction if both queries are successful
        mysqli_commit($conn);

        $data = [
            'status' => 200,
            'message' => 'Values deleted successfully',
        ];
        header("HTTP/1.0 200 success");
        return json_encode($data);
    } else {
        // Rollback the transaction if any query fails
        mysqli_rollback($conn);

        $data = [
            'status' => 404,
            'message' => 'Values not found or deletion failed',
        ];
        header("HTTP/1.0 404 Not found");
        return json_encode($data);
    }
}


//update
function updateExcercise($excerciseInput, $excerciseparams){

    global $conn;

    if(!isset($excerciseparams['exe_num'])) {
        return error422('module number is not found in url');
    } elseif($excerciseparams['exe_num']== null){
        return error422('Enter the number');
    }

 
    $exe_num = mysqli_real_escape_string($conn, $excerciseInput['exe_num']);
    $exe_answer = mysqli_real_escape_string($conn, $excerciseInput['exe_answer']);
    $exe_question = mysqli_real_escape_string($conn, $excerciseInput['exe_question']);


    if(empty(trim($exe_answer))){
        return error422('enter answer');

    }elseif(empty(trim($exe_question))){
        return error422('enter question');

    }
    else {

        $query = "UPDATE `edu_module_exercises` SET `exe_question` = '$exe_question', `exe_answer` = '$exe_answer' WHERE `exe_num` = '$exe_num' LIMIT 1";

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

//post

function storeModulesExercises($moduleInput) {
    global $conn;

    $exe_num = mysqli_real_escape_string($conn,$moduleInput['exe_num']);
    $mod_num = mysqli_real_escape_string($conn,$moduleInput['mod_num']);
    $exe_question = mysqli_real_escape_string($conn,$moduleInput['exe_question']);
    $exe_answer = mysqli_real_escape_string($conn,$moduleInput['exe_answer']);
    $exe_sentence_rule = mysqli_real_escape_string($conn,$moduleInput['exe_sentence_rule']);


    if(empty(trim($mod_num))){

        return error422('enter mod num');

    }elseif(empty(trim($exe_question))){
        return error422('enter exe_question');

    }elseif(empty(trim($exe_answer))){

        return error422('enter exe_answer');
    }
    else {
        $query = "INSERT INTO `edu_module_exercises`(`mod_num`, `exe_question`, `exe_answer`, `exe_sentence_rule`) VALUES ($mod_num,'$exe_question','$exe_answer','1')";
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



//get

function getModuleList() {
    global $conn;

    $query = "SELECT * FROM `edu_module_exercises`";
    $query_run = mysqli_query($conn,$query);

    if($query_run){
        if(mysqli_num_rows($query_run) > 0){
        
            $res = mysqli_fetch_all($query_run,MYSQLI_ASSOC);

            // Return a JSON array of Customer objects
            $data = array();
            foreach ($res as $row) {
                $customer = array(
                    'exe_num' => $row['exe_num'],
                    'mod_num' => $row['mod_num'],
                    'exe_question' => $row['exe_question'],
                    'exe_answer' => $row['exe_answer'],
                    'exe_sentence_rule' => $row['exe_sentence_rule'],

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




//1 to 1 data fetching

// function getmodulesexcersicesList(){

// global $conn;



// $query = "SELECT * FROM `edu_module_exercises`";
// $result = mysqli_query($conn,$query);

// if($result){

//  if(mysqli_num_rows($result) > 0){
 
//     $res = mysqli_fetch_assoc($result);
//     $data =$res;
//     header("HTTP/1.0 200  Success");
//     return json_encode($data);


//  }else{
//  $data = [
//         'status' => 404,
//         'message' => 'No edu_preliminary_trans_questions Found',
//     ];
//     header("HTTP/1.0 404  Not found");
//     return json_encode($data);
//  }

// }else{
//     $data = [
//         'status' => 500,
//         'message' => 'Internal Server Error',
//     ];
//     header("HTTP/1.0 500  Internal Server Error");
//     return json_encode($data);
// }

// }

?>
