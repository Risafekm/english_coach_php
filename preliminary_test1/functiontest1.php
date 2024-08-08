<?php

require 'connection.php';

// Error message
function error422($message){
    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 Unprocessable Entity");
    echo json_encode($data);
    exit();
}

// Delete
function deleteMCQuestion($questionId) {
    global $conn;

    // Delete options for the question
    $deleteOptionsQuery = "DELETE FROM edu_preliminary_mcq_options WHERE prelim_mcques_num = '$questionId'";
    $deleteOptionsResult = mysqli_query($conn, $deleteOptionsQuery);

    if (!$deleteOptionsResult) {
        $error = mysqli_error($conn);
        echo json_encode(array("error" => "Failed to delete options for the question: $error"));
        return;
    }

    // Delete the question itself
    $deleteQuestionQuery = "DELETE FROM edu_preliminary_mc_questions WHERE prelim_mcques_num = '$questionId'";
    $deleteQuestionResult = mysqli_query($conn, $deleteQuestionQuery);

    if (!$deleteQuestionResult) {
        $error = mysqli_error($conn);
        echo json_encode(array("error" => "Failed to delete the question: $error"));
        return;
    }

    // Return success message
    echo json_encode(array("success" => true, "message" => "Question and its options deleted successfully"));
}

// Get
function getQuestionsAndOptions() {
    global $conn;

    $query = "SELECT 
                edu_preliminary_mc_questions.prelim_mcques_num,
                edu_preliminary_mc_questions.prelim_mcques_question, 
                JSON_ARRAYAGG(edu_preliminary_mcq_options.prelim_mcq_answer) AS options,
                (SELECT edu_preliminary_mcq_options.prelim_mcq_answer FROM 
                edu_preliminary_mcq_options WHERE
                edu_preliminary_mcq_options.prelim_mcq_id = edu_preliminary_mc_questions.prelim_mcq_id) AS mcq_answer
              FROM 
                edu_preliminary_mc_questions
              JOIN 
                edu_preliminary_mcq_options ON edu_preliminary_mc_questions.prelim_mcques_num = edu_preliminary_mcq_options.prelim_mcques_num
              GROUP BY 
                edu_preliminary_mc_questions.prelim_mcques_num";
 
    $result = mysqli_query($conn, $query);

    if ($result) {
        $questions = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $question = array(
                "question_no" => $row['prelim_mcques_num'],
                "question" => $row['prelim_mcques_question'],
                "options" => json_decode($row['options']),
                "mcq_answer" => $row['mcq_answer']
            );
            $questions[] = $question;
        }
        
        echo json_encode($questions);
    } else {
        echo json_encode(array("error" => "Failed to fetch data from database"));
    }
}

// Post

function insertPreliminaryQuestionWithOptions($questionData, $options) {
    global $conn;

    $questionText = mysqli_real_escape_string($conn, $questionData['prelim_mcques_question']);
    $tNum = 10001; 

    $questionQuery = "INSERT INTO edu_preliminary_mc_questions (t_num, prelim_mcques_question) VALUES ('$tNum', '$questionText')";
    $result1 = mysqli_query($conn, $questionQuery);

    if (!$result1) {
        echo json_encode(array("error" => "Failed to insert question into database"));
        return;
    }

    $questionId = mysqli_insert_id($conn);

    if (count($options) !== 4) {
        echo json_encode(array("error" => "Exactly 4 options are required"));
        return;
    }

    foreach ($options as $optionText) {
        $optionText = mysqli_real_escape_string($conn, $optionText);
        $optionQuery = "INSERT INTO edu_preliminary_mcq_options (prelim_mcques_num, prelim_mcq_answer) VALUES ('$questionId', '$optionText')";
        $result2 = mysqli_query($conn, $optionQuery);

        if (!$result2) {
            $error = mysqli_error($conn);
            echo json_encode(array("error" => "Failed to insert option into database: $error"));
            return;
        }
    }

    $enteredAnswer = isset($questionData['prelim_mcq_answer']) ? $questionData['prelim_mcq_answer'] : null;

    if (empty($enteredAnswer)) {
        echo json_encode(array("error" => "Entered answer is missing. Please make sure to provide an answer."));
        return;
    }

    if (!in_array($enteredAnswer, $options)) {
        echo json_encode(array("error" => "Entered answer does not match any MCQ option"));
        return;
    }

    $optionIdQuery = "SELECT prelim_mcq_id FROM edu_preliminary_mcq_options WHERE prelim_mcques_num = '$questionId' AND prelim_mcq_answer = '$enteredAnswer'";
    $optionIdResult = mysqli_query($conn, $optionIdQuery);

    if (!$optionIdResult) {
        $error = mysqli_error($conn);
        echo json_encode(array("error" => "Failed to fetch prelim_mcq_id from database: $error"));
        return;
    }

    $optionIdRow = mysqli_fetch_assoc($optionIdResult);
    $optionId = $optionIdRow['prelim_mcq_id'];

    $updateQuestionQuery = "UPDATE edu_preliminary_mc_questions SET prelim_mcq_id = '$optionId' WHERE prelim_mcques_num = '$questionId'";
    $updateResult = mysqli_query($conn, $updateQuestionQuery);

    if (!$updateResult) {
        $error = mysqli_error($conn);
        echo json_encode(array("error" => "Failed to update question with prelim_mcq_id: $error"));
        return;
    }

    echo json_encode(array("success" => true, "message" => "Question and options inserted successfully"));
}

// Update
function updateMCQuestionWithOptions($questionId, $questionData, $options) {
    global $conn;

    $questionText = mysqli_real_escape_string($conn, $questionData['prelim_mcques_question']);

    $updateQuestionQuery = "UPDATE edu_preliminary_mc_questions SET prelim_mcques_question = '$questionText' WHERE prelim_mcques_num = '$questionId'";
    $updateQuestionResult = mysqli_query($conn, $updateQuestionQuery);

    if (!$updateQuestionResult) {
        $error = mysqli_error($conn);
        echo json_encode(array("error" => "Failed to update question in database: $error"));
        return;
    }

    $deleteOptionsQuery = "DELETE FROM edu_preliminary_mcq_options WHERE prelim_mcques_num = '$questionId'";
    $deleteOptionsResult = mysqli_query($conn, $deleteOptionsQuery);

    if (!$deleteOptionsResult) {
        $error = mysqli_error($conn);
        echo json_encode(array("error" => "Failed to delete existing options for the question: $error"));
        return;
    }

    if (count($options) !== 4) {
        echo json_encode(array("error" => "Exactly 4 options are required"));
        return;
    }

    foreach ($options as $optionText) {
        $optionText = mysqli_real_escape_string($conn, $optionText);
        $optionQuery = "INSERT INTO edu_preliminary_mcq_options (prelim_mcques_num, prelim_mcq_answer) VALUES ('$questionId', '$optionText')";
        $insertOptionResult = mysqli_query($conn, $optionQuery);

        if (!$insertOptionResult) {
            $error = mysqli_error($conn);
            echo json_encode(array("error" => "Failed to insert option into database: $error"));
            return;
        }
    }

    $enteredAnswer = isset($questionData['prelim_mcq_answer']) ? $questionData['prelim_mcq_answer'] : null;

    if (empty($enteredAnswer)) {
        echo json_encode(array("error" => "Entered answer is missing. Please make sure to provide an answer."));
        return;
    }

    if (!in_array($enteredAnswer, $options)) {
        echo json_encode(array("error" => "Entered answer does not match any MCQ option"));
        return;
    }

    $optionIdQuery = "SELECT prelim_mcq_id FROM edu_preliminary_mcq_options WHERE prelim_mcques_num = '$questionId' AND prelim_mcq_answer = '$enteredAnswer'";
    $optionIdResult = mysqli_query($conn, $optionIdQuery);

    if (!$optionIdResult) {
        $error = mysqli_error($conn);
        echo json_encode(array("error" => "Failed to fetch prelim_mcq_id from database: $error"));
        return;
    }

    $optionIdRow = mysqli_fetch_assoc($optionIdResult);
    $optionId = $optionIdRow['prelim_mcq_id'];

    $updateQuestionWithOptionIdQuery = "UPDATE edu_preliminary_mc_questions SET prelim_mcq_id = '$optionId' WHERE prelim_mcques_num = '$questionId'";
    $updateQuestionWithOptionIdResult = mysqli_query($conn, $updateQuestionWithOptionIdQuery);

    if (!$updateQuestionWithOptionIdResult) {
        $error = mysqli_error($conn);
        echo json_encode(array("error" => "Failed to update question with prelim_mcq_id: $error"));
        return;
    }

    echo json_encode(array("success" => true, "message" => "Question and options updated successfully"));
}

?>
