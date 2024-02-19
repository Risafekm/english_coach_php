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


//post

function storePreliminaryTest2List($preliminaryInput) {
    global $conn;

    $prelim_trans_question = mysqli_real_escape_string($conn, $preliminaryInput['prelim_trans_question']);
    $t_num = mysqli_real_escape_string($conn, $preliminaryInput['t_num']);
    $prelim_trans_ques_num = mysqli_real_escape_string($conn, $preliminaryInput['prelim_trans_ques_num']);
    $prelim_trans_answer = mysqli_real_escape_string($conn, $preliminaryInput['prelim_trans_answer']);

    if (empty(trim($prelim_trans_question)) || empty(trim($prelim_trans_answer))) {
        return error422('Enter both prelim_trans_question and prelim_trans_answer');
    } else {
        // Start a transaction to ensure data consistency
        mysqli_begin_transaction($conn);

        try {
            // Insert into edu_preliminary_trans_questions
            $query_questions = "INSERT INTO edu_preliminary_trans_questions(prelim_trans_question, t_num) VALUES ('$prelim_trans_question', '1')";
            $result_questions = mysqli_query($conn, $query_questions);

            if (!$result_questions) {
                throw new Exception(mysqli_error($conn));
            }

            // Get the auto-generated ID from the first insert
            $prelim_trans_ques_id = mysqli_insert_id($conn);

            // Insert into edu_preliminary_translations
            $query_translations = "INSERT INTO edu_preliminary_translations(prelim_trans_ques_num, prelim_trans_answer) VALUES ('$prelim_trans_ques_id', '$prelim_trans_answer')";
            $result_translations = mysqli_query($conn, $query_translations);

            if (!$result_translations) {
                throw new Exception(mysqli_error($conn));
            }

            // If everything is successful, commit the transaction
            mysqli_commit($conn);

            $data = [
                'status' => 201,
                'message' => 'Data inserted successfully',
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);

        } catch (Exception $e) {
            // If any step fails, rollback the transaction
            mysqli_rollback($conn);

            $data = [
                'status' => 500,
                'message' => 'Internal Server Error: ' . $e->getMessage(),
            ];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data);
        }
    }
}

function storeModule($moduleInput) {
    global $conn;

    $exe_num = mysqli_real_escape_string($conn,$moduleInput['exe_num']);
    $mod_num = mysqli_real_escape_string($conn,$moduleInput['mod_num']);
    $exe_question = mysqli_real_escape_string($conn,$moduleInput['exe_question']);
    $exe_answer = mysqli_real_escape_string($conn,$moduleInput['exe_answer']);
    $exe_sentence_rule = mysqli_real_escape_string($conn,$moduleInput['exe_sentence_rule']);


    if(empty(trim($exe_num))){

        return error422('enter exe_num');

    }elseif(empty(trim($mod_num))){
        return error422('enter mod_num');

    }elseif(empty(trim($exe_question))){
        return error422('enter exe_question');

    }elseif(empty(trim($exe_answer))){
        return error422('enter exe_answer');

    }elseif(empty(trim($exe_sentence_rule))){

        return error422('enter exe_sentence_rule');
    } 
    else {
        $query = "INSERT INTO `edu_module_exercises`(`exe_num`,`mod_num`,`exe_question`,`exe_answer`,`exe_sentence_rule`) VALUES (`$exe_num`,`$mod_num`,`$exe_question`,`$exe_answer`,`$exe_sentence_rule`)";
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




function getModuleList() {
    global $conn;

    $query = "SELECT exe_num FROM `edu_module_exercises`";
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