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

//postFunction

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

?>