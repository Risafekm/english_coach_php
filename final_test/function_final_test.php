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



function storefinaltest($prilimsInput) {
    global $conn;

    $final_ques_number = mysqli_real_escape_string($conn, $prilimsInput['final_ques_number']);
    $final_questions = mysqli_real_escape_string($conn, $prilimsInput['final_questions']);
    $final_answers = mysqli_real_escape_string($conn, $prilimsInput['final_answers']);
    $final_ans_id = mysqli_real_escape_string($conn, $prilimsInput['final_ans_id']);


    if (empty(trim($final_answers)) || empty(trim($final_questions))) {
        return error422('Enter both final_questions and final_answers');
    } else {
        // Start a transaction to ensure data consistency
        mysqli_begin_transaction($conn);

        try {
            // Insert into edu_preliminary_trans_questions
            $query_questions = "INSERT INTO edu_final_questions(final_ques_number,final_questions) VALUES ('$final_ques_number','$final_questions')";
            $result_questions = mysqli_query($conn, $query_questions);

            if (!$result_questions) {
                throw new Exception(mysqli_error($conn));
            }

            // Get the auto-generated ID from the first insert
            $final_ques_number = mysqli_insert_id($conn);

            // Insert into edu_preliminary_translations
            $query_translations = "INSERT INTO edu_final_answers (final_ques_number, final_answers) VALUES ('$final_ques_number', '$final_answers')";
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


  