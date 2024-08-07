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

    // Perform the SQL query to fetch questions, options, and correct answers
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
 
    // Execute the query
    $result = mysqli_query($conn, $query);

    // Check if query was successful
    if ($result) {
        // Fetch data and store it in an array
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
        
        // Return the questions and options array as JSON
        echo json_encode($questions);
    } else {
        // Handle query error
        echo json_encode(array("error" => "Failed to fetch data from database"));
    }
}

// Post

// Insert question with options
function insertPreliminaryQuestionWithOptions($questionData, $options) {
    global $conn;

    // Insert the question
    $questionText = mysqli_real_escape_string($conn, $questionData['prelim_mcques_question']);
    $tNum = 10001; // Assuming t_num is a fixed value

    $questionQuery = "INSERT INTO edu_preliminary_mc_questions (t_num, prelim_mcques_question) VALUES ('$tNum', '$questionText')";
    $result1 = mysqli_query($conn, $questionQuery);

    if (!$result1) {
        echo json_encode(array("error" => "Failed to insert question into database"));
        return;
    }

    // Retrieve the auto-generated question_id
    $questionId = mysqli_insert_id($conn);

    // Insert options for the question
    if (count($options) !== 4) {
        echo json_encode(array("error" => "Exactly 4 options are required"));
        return;
    }

    foreach ($options as $optionText) {
        // Escape the option text
        $optionText = mysqli_real_escape_string($conn, $optionText);

        // Insert the option into the database
        $optionQuery = "INSERT INTO edu_preliminary_mcq_options (prelim_mcques_num, prelim_mcq_answer) VALUES ('$questionId', '$optionText')";
        $result2 = mysqli_query($conn, $optionQuery);

        if (!$result2) {
            $error = mysqli_error($conn);
            echo json_encode(array("error" => "Failed to insert option into database: $error"));
            return;
        }
    }

    // Retrieve the entered answer from $questionData
    $enteredAnswer = isset($questionData['prelim_mcq_answer']) ? $questionData['prelim_mcq_answer'] : null;

    // Check if the entered answer is provided and not empty
    if (empty($enteredAnswer)) {
        echo json_encode(array("error" => "Entered answer is missing. Please make sure to provide an answer."));
        return;
    }

    // Check if the entered answer matches any MCQ option
    if (!in_array($enteredAnswer, $options)) {
        echo json_encode(array("error" => "Entered answer does not match any MCQ option"));
        return;
    }

    // Fetch the prelim_mcq_id from the result
    $optionIdQuery = "SELECT prelim_mcq_id FROM edu_preliminary_mcq_options WHERE prelim_mcques_num = '$questionId' AND prelim_mcq_answer = '$enteredAnswer'";
    $optionIdResult = mysqli_query($conn, $optionIdQuery);

    if (!$optionIdResult) {
        $error = mysqli_error($conn);
        echo json_encode(array("error" => "Failed to fetch prelim_mcq_id from database: $error"));
        return;
    }

    // Check if there is a row fetched
    if (mysqli_num_rows($optionIdResult) == 0) {
        echo json_encode(array("error" => "No prelim_mcq_id returned by the query."));
        return;
    }

    // Fetch the prelim_mcq_id from the result
    $optionIdRow = mysqli_fetch_assoc($optionIdResult);

    // Update the prelim_mcq_id in the edu_preliminary_mc_questions table
    $updateQuery = "UPDATE edu_preliminary_mc_questions SET prelim_mcq_id = '{$optionIdRow['prelim_mcq_id']}' WHERE prelim_mcques_num = '$questionId'";
    $updateResult = mysqli_query($conn, $updateQuery);

    if (!$updateResult) {
        $error = mysqli_error($conn);
        echo json_encode(array("error" => "Failed to update prelim_mcq_id in edu_preliminary_mc_questions table: $error"));
        return;
    }

    // Return success message
    echo json_encode(array("success" => true, "prelim_mcq_id" => $optionIdRow['prelim_mcq_id']));
}


// Update

function updateMCQuestionWithOptions($questionId, $questionData, $options) {
    global $conn;

    // Update the question
    $questionText = mysqli_real_escape_string($conn, $questionData['prelim_mcques_question']);
    $updateQuery = "UPDATE edu_preliminary_mc_questions SET prelim_mcques_question = '$questionText' WHERE prelim_mcques_num = '$questionId'";
    $result1 = mysqli_query($conn, $updateQuery);

    if (!$result1) {
        echo json_encode(array("error" => "Failed to update question in database"));
        return;
    }

    // Delete existing options for the question
    $deleteQuery = "DELETE FROM edu_preliminary_mcq_options WHERE prelim_mcques_num = '$questionId'";
    $result2 = mysqli_query($conn, $deleteQuery);

    if (!$result2) {
        echo json_encode(array("error" => "Failed to delete existing options for the question"));
        return;
    }

    // Insert new options for the question
    if (count($options) != 4) {
        echo json_encode(array("error" => "Exactly four options are required"));
        return;
    }

    foreach ($options as $optionText) {
        $optionText = mysqli_real_escape_string($conn, $optionText);
        $insertOptionQuery = "INSERT INTO edu_preliminary_mcq_options (prelim_mcques_num, prelim_mcq_answer) VALUES ('$questionId', '$optionText')";
        $result3 = mysqli_query($conn, $insertOptionQuery);

        if (!$result3) {
            echo json_encode(array("error" => "Failed to insert options into database"));
            return;
        }
    }

    // Retrieve the entered answer from $questionData
    $enteredAnswer = isset($questionData['prelim_mcq_answer']) ? $questionData['prelim_mcq_answer'] : null;

    // Check if the entered answer is provided and not empty
    if (!isset($questionData['prelim_mcq_answer']) || empty($questionData['prelim_mcq_answer'])) {
        echo json_encode(array("error" => "Entered answer is missing. Please make sure to provide an answer."));
        return;
    }

    // Check if the entered answer is provided
    if ($enteredAnswer === null || $enteredAnswer === '') {
        echo json_encode(array("error" => "Entered answer is missing"));
        return;
    }

    // If the entered answer is valid and provided, proceed with further validation
    // Check if the entered answer matches any MCQ option
    if (!in_array($enteredAnswer, $options)) {
        // If the entered answer is not in the options array, return an error
        echo json_encode(array("error" => "Entered answer does not match any MCQ option"));
        return;
    }

    // If the entered answer matches an option, return success message along with the entered answer
    echo json_encode(array("success" => true, "enteredAnswer" => $enteredAnswer));

    // Fetch the prelim_mcq_id from the result
    $optionIdQuery = "SELECT prelim_mcq_id FROM edu_preliminary_mcq_options WHERE prelim_mcques_num = '$questionId' AND prelim_mcq_answer = '$enteredAnswer'";
    $optionIdResult = mysqli_query($conn, $optionIdQuery);

    if (!$optionIdResult) {
        // Handle the error
        $error = mysqli_error($conn);
        echo json_encode(array("error" => "Failed to fetch prelim_mcq_id from database: $error"));
        return;
    }

    // Check if there is a row fetched
    if (mysqli_num_rows($optionIdResult) == 0) {
        // Handle the case where no row is fetched
        echo json_encode(array("error" => "No prelim_mcq_id returned by the query."));
        return;
    }

    // Fetch the prelim_mcq_id from the result
    $optionIdRow = mysqli_fetch_assoc($optionIdResult);

    // Check if there is a row fetched
    if (!$optionIdRow) {
        // Handle the case where no row is fetched
        echo json_encode(array("error" => "No prelim_mcq_id returned by the query."));
        return;
    }

    // Update the prelim_mcq_id in the edu_preliminary_mc_questions table
    $trialnumQuery = "UPDATE edu_preliminary_mc_questions SET prelim_mcq_id = '$optionIdRow[prelim_mcq_id]' WHERE prelim_mcques_num = '$questionId'";
    $trialnumResult = mysqli_query($conn, $trialnumQuery);

    // Check if the update was successful
    if (!$trialnumResult) {
        // Handle the update failure
        $error = mysqli_error($conn);
        echo json_encode(array("error" => "Failed to update prelim_mcq_id in edu_preliminary_mc_questions table:$error"));
        return;
    }

    // Return success message
    echo json_encode(array("success" => true, "prelim_mcq_id" => $optionIdRow['prelim_mcq_id']));
}

// Checking request method
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $action = $_GET['action'];

    if ($action == 'insert') {
        $questionData = json_decode(file_get_contents('php://input'), true);
        if (isset($questionData['prelim_mcques_question']) && isset($questionData['options'])) {
            insertPreliminaryQuestionWithOptions($questionData, $questionData['options']);
        } else {
            echo json_encode(array("error" => "Invalid input data"));
        }
    } elseif ($action == 'update') {
        $questionData = json_decode(file_get_contents('php://input'), true);
        $questionId = $_GET['prelim_mcques_num'];
        if (isset($questionId) && isset($questionData['prelim_mcques_question']) && isset($questionData['options'])) {
            updateMCQuestionWithOptions($questionId, $questionData, $questionData['options']);
        } else {
            echo json_encode(array("error" => "Invalid input data"));
        }
    }
} elseif ($method == 'GET') {
    getQuestionsAndOptions();
} elseif ($method == 'DELETE') {
    $questionId = $_GET['prelim_mcques_num'];
    if (isset($questionId)) {
        deleteMCQuestion($questionId);
    } else {
        echo json_encode(array("error" => "Invalid input data"));
    }
} else {
    // If the request method is not supported
    echo json_encode(array("error" => "Unsupported request method"));
}

?>
