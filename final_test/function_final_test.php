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

//Get Method
function getFinalTestList(){
 
    global $conn;
    
    $query = "SELECT q.final_questions,q.final_ques_number,a.final_answers FROM edu_final_questions q INNER JOIN edu_final_answers a ON q.final_ques_number = a.final_ques_number";
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

//Update Method

function updateFinalTestList($testFinalInput, $testFinalParams)
{
    global $conn;

    if (!isset($testFinalParams['final_ques_number'])) {
        return error422('id not found');
    }

    $finalNum = mysqli_real_escape_string($conn, $testFinalParams['final_ques_number']);
    $finalQuestion = mysqli_real_escape_string($conn, $testFinalInput['final_questions']);
    $finalAnswer = mysqli_real_escape_string($conn, $testFinalInput['final_answers']); // Assuming prelim_trans_answer is part of the input

    if (empty(trim($finalQuestion))) {
        return error422('Enter the final_question');
    } else {

        // Update edu_preliminary_trans_questions table
        $queryQuestions = "UPDATE `edu_final_questions` SET `final_questions` = '$finalQuestion' WHERE `final_ques_number` = '$finalNum'";
        $resultQuestions = mysqli_query($conn, $queryQuestions);

        // Update edu_preliminary_translations table
        $queryAnswers = "UPDATE `edu_final_answers` SET `final_answers` = '$finalAnswer' WHERE `final_ques_number` = '$finalNum'";
        $resultAnswers = mysqli_query($conn, $queryAnswers);

        if ($resultQuestions && $resultAnswers) {
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

    // Delete Method

    function deleteFinalTest($finalTestParams) {
        global $conn;
    
        if (!isset($finalTestParams['final_ques_number'])) {
            return error422("id not found ");
        }
    
        $finalNum = mysqli_real_escape_string($conn, $finalTestParams['final_ques_number']);
    
        // Start a transaction to ensure both queries are executed or none
        mysqli_begin_transaction($conn);
    
        // Delete from edu_preliminary_trans_questions table
        $query1 = "DELETE FROM `edu_final_questions` WHERE `final_ques_number` = $finalNum LIMIT 1";
        $result1 = mysqli_query($conn, $query1);
    
        // Delete from edu_preliminary_translations table
        $query2 = "DELETE FROM `edu_final_answers` WHERE `final_ques_number` = $finalNum LIMIT 1";
        $result2 = mysqli_query($conn, $query2);
    
        if ($result1 && $result2) {
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
?>