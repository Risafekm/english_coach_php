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

function deletePrelims($prilimsparams) {
    global $conn;

    if (!isset($prilimsparams['topic_que_num'])) {
        return error422("id not found ");
    }

    $topic_que_num = mysqli_real_escape_string($conn, $prilimsparams['topic_que_num']);

    // Start a transaction to ensure both queries are executed or none
    mysqli_begin_transaction($conn);

    // Delete from edu_topic_questions table
    $query1 = "DELETE FROM `edu_topic_questions` WHERE `topic_que_num` = $topic_que_num LIMIT 1";
    $result1 = mysqli_query($conn, $query1);

    // Delete from edu_topic_answers table
    $query2 = "DELETE FROM `edu_topic_answers` WHERE `topic_que_num` = $topic_que_num LIMIT 1";
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
function updatePrilims($prilimsInput, $prilimsparams)
{
    global $conn;

    if (!isset($prilimsparams['topic_que_num'])) {
        return error422('id not found');
    }

    $topicquenum = mysqli_real_escape_string($conn, $prilimsInput['topic_que_num']);
    $topic_que_question = mysqli_real_escape_string($conn, $prilimsInput['topic_que_question']);
    $topic_ans_answer = mysqli_real_escape_string($conn, $prilimsInput['topic_ans_answer']); // Assuming prelim_trans_answer is part of the input

    if (empty(trim($topic_que_question))) {
        return error422('Enter the topic_que_question');
    } else {

        // Update topic_que_question table
        $queryQuestions = "UPDATE `edu_topic_questions` SET `topic_que_question` = '$topic_que_question' WHERE `topic_que_num` = '$topicquenum'";
        $resultQuestions = mysqli_query($conn, $queryQuestions);

        // Update edu_topic_answers table
        $queryAnswers = "UPDATE `edu_topic_answers` SET `topic_ans_answer` = '$topic_ans_answer' WHERE `topic_que_num` = '$topicquenum'";
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

function storeprelims($prilimsInput) {
    global $conn;

    $topic_que_num = mysqli_real_escape_string($conn, $prilimsInput['topic_que_num']);
    $t_num = mysqli_real_escape_string($conn, $prilimsInput['t_num']);
    $topic_ans_answer = mysqli_real_escape_string($conn, $prilimsInput['topic_ans_answer']);
    $topic_que_question = mysqli_real_escape_string($conn, $prilimsInput['topic_que_question']);
    $modid = mysqli_real_escape_string($conn, $prilimsInput['mod_id']);


    if (empty(trim($topic_que_question)) || empty(trim($topic_ans_answer))) {
        return error422('Enter both topic_que_question and topic_ans_answer');
    } else {
        // Start a transaction to ensure data consistency
        mysqli_begin_transaction($conn);

        try {
            // Insert into edu_preliminary_trans_questions
            $query_questions = "INSERT INTO edu_topic_questions(mod_id,t_num, topic_que_question) VALUES ('22','1','$topic_que_question')";
            $result_questions = mysqli_query($conn, $query_questions);

            if (!$result_questions) {
                throw new Exception(mysqli_error($conn));
            }

            // Get the auto-generated ID from the first insert
            $topic_que_num = mysqli_insert_id($conn);

            // Insert into edu_preliminary_translations
            $query_translations = "INSERT INTO edu_topic_answers(topic_que_num, topic_ans_answer) VALUES ('$topic_que_num', '$topic_ans_answer')";
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
function getPrilimstList() {
    global $conn;

    $query = "SELECT `edu_topic_answers`.`topic_que_num`,`edu_topic_answers`.`topic_ans_answer`, `edu_topic_questions`.`topic_que_question` FROM `edu_topic_answers`, `edu_topic_questions` WHERE `edu_topic_answers`.`topic_que_num` = `edu_topic_questions`.`topic_que_num`";

    $query_run = mysqli_query($conn, $query);
    
    if ($query_run) {
        if (mysqli_num_rows($query_run) > 0) {
            $data = array();
            while ($row = mysqli_fetch_assoc($query_run)) {
                // Check if the keys exist in the $row array before accessing them
                if (isset($row['topic_ans_answer']) && isset($row['topic_que_question'])) {
                    $question = array(
                        'topic_que_num' => $row['topic_que_num'],
                        'topic_ans_answer' => $row['topic_ans_answer'],
                        'topic_que_question' => $row['topic_que_question'],
                    );
                    $data[] = $question;
                }
            }
            header("HTTP/1.0 200 Success");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'No Student Found', // corrected typo here
            ];
            header("HTTP/1.0 404 Not Found");
            return json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}