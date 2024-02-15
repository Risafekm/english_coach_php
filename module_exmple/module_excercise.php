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


//Update District list

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

//Update 
function updateExcercise($excerciseInput, $excerciseparams)
{
    global $conn;

    if (!isset($excerciseparams['exe_num'])) {
        return error422('id not found');
    }

    $exe_num = mysqli_real_escape_string($conn, $excerciseInput['exe_num']);
    $exe_answer = mysqli_real_escape_string($conn, $excerciseInput['exe_answer']);
    $exe_question = mysqli_real_escape_string($conn, $excerciseInput['exe_question']); // Assuming prelim_trans_answer is part of the input

    if (empty(trim($exe_question))) {
        return error422('Enter the exe_question');
    }  elseif (empty(trim($exe_answer))) {
        return error422('Enter the exe_answer');
    } else {

        // Update edu_module_exercises table
        $queryQuestions = "UPDATE `edu_module_exercises` SET `exe_question` = '$exe_question', `exe_answer` = '$exe_answer' WHERE `exe_num` = '$exe_num'";
        $resultQuestions = mysqli_query($conn, $queryQuestions);


        if ($resultQuestions) {
            $data = [
                'status' => 200,
                'message' => 'updated successfully',
            ];
            header("HTTP/1.0 200 OK");
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


