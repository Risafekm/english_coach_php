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
function deletePreliminaryTest2($preliminaryTest2params) {
    global $conn;

    if (!isset($preliminaryTest2params['prelim_trans_ques_num'])) {
        return error422("id not found ");
    }

    $prelimNum = mysqli_real_escape_string($conn, $preliminaryTest2params['prelim_trans_ques_num']);

    // Start a transaction to ensure both queries are executed or none
    mysqli_begin_transaction($conn);

    // Delete from edu_preliminary_trans_questions table
    $query1 = "DELETE FROM `edu_preliminary_trans_questions` WHERE `prelim_trans_ques_num` = $prelimNum LIMIT 1";
    $result1 = mysqli_query($conn, $query1);

    // Delete from edu_preliminary_translations table
    $query2 = "DELETE FROM `edu_preliminary_translations` WHERE `prelim_trans_ques_num` = $prelimNum LIMIT 1";
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

//Update 
function updatePreliminaryTest2List($preliminaryInput, $preliminaryTest2params)
{
    global $conn;

    if (!isset($preliminaryTest2params['prelim_trans_ques_num'])) {
        return error422('id not found');
    }

    $prelimNum = mysqli_real_escape_string($conn, $preliminaryTest2params['prelim_trans_ques_num']);
    $prelimQuestion = mysqli_real_escape_string($conn, $preliminaryInput['prelim_trans_question']);
    $prelimAnswer = mysqli_real_escape_string($conn, $preliminaryInput['prelim_trans_answer']); // Assuming prelim_trans_answer is part of the input

    if (empty(trim($prelimQuestion))) {
        return error422('Enter the prelim_trans_question');
    } else {

        // Update edu_preliminary_trans_questions table
        $queryQuestions = "UPDATE `edu_preliminary_trans_questions` SET `prelim_trans_question` = '$prelimQuestion' WHERE `prelim_trans_ques_num` = '$prelimNum'";
        $resultQuestions = mysqli_query($conn, $queryQuestions);

        // Update edu_preliminary_translations table
        $queryAnswers = "UPDATE `edu_preliminary_translations` SET `prelim_trans_answer` = '$prelimAnswer' WHERE `prelim_trans_ques_num` = '$prelimNum'";
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

//getFunction

function getPreliminaryTest2List(){
 
global $conn;

$query = "SELECT q.prelim_trans_question,q.prelim_trans_ques_num,a.prelim_trans_answer FROM edu_preliminary_trans_questions q INNER JOIN edu_preliminary_translations a ON q.prelim_trans_ques_num = a.prelim_trans_ques_num";
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

function getPreliminaryTest2($preliminaryTest2params){

global $conn;


if($preliminaryTest2params['prelim_trans_ques_num'] == null){
    return error422('Enter your id');
   }

$prelimNum = mysqli_real_escape_string($conn, $preliminaryTest2params['prelim_trans_ques_num']);

$query = "SELECT q.prelim_trans_question,q.prelim_trans_ques_num,a.prelim_trans_answer FROM edu_preliminary_trans_questions q INNER JOIN edu_preliminary_translations a ON q.prelim_trans_ques_num = a.prelim_trans_ques_num WHERE q.prelim_trans_ques_num = '$prelimNum' LIMIT 1";
// "SELECT * FROM `edu_preliminary_trans_questions` WHERE `prelim_trans_ques_num` = '$prelimNum' LIMIT 1";
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