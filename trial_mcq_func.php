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

// Function to fetch questions and options from the database
function getQuestionsAndOptions() {
    global $conn;

    // Perform the SQL query to fetch questions, options, and correct answers
    $query = "SELECT 
                edu_trial_mcq_questions.trail_mcq_num,
                edu_trial_mcq_questions.trail_mcq_question, 
                JSON_ARRAYAGG(edu_trial_mcq_options.trial_mcq_answer) AS options,
                edu_trial_mcq_options.trial_mcq_answer AS mcq_answer
              FROM 
                edu_trial_mcq_questions
              JOIN 
                edu_trial_mcq_options ON edu_trial_mcq_questions.trail_mcq_num = edu_trial_mcq_options.trial_mcq_num
              GROUP BY 
                edu_trial_mcq_questions.trail_mcq_num";

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Check if query was successful
    if ($result) {
        // Fetch data and store it in an array
        $questions = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $question = array(
                "question_no" => $row['trail_mcq_num'],
                "question" => $row['trail_mcq_question'],
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

//post method

// function insertMCQuestionWithOptions($questionData, $options) {
//     global $conn;

//     // Insert the question
//     $questionText = mysqli_real_escape_string($conn, $questionData['trail_mcq_question']);
//     $moduleId = 10001; // Assuming mod_id is a fixed value

//     $questionQuery = "INSERT INTO edu_trial_mcq_questions (mod_id, trail_mcq_question) VALUES ('1001', '$questionText')";
//     $result1 = mysqli_query($conn, $questionQuery);

//     if (!$result1) {
//         echo json_encode(array("error" => "Failed to insert question into database"));
//         return;
//     }

//     // Retrieve the auto-generated question_id
//     $questionId = mysqli_insert_id($conn);

//     // Insert options for the question
//     for ($i = 0; $i < 3; $i++) {
//         // Check if options array has enough items
//         if (isset($options[$i])) {
//             // Escape the option text
//             $optionText = mysqli_real_escape_string($conn, $options[$i]);
            
//             // Insert the option into the database
//             $optionQuery = "INSERT INTO edu_trial_mcq_options (trial_mcq_num, trial_mcq_answer) VALUES ('$questionId', '$optionText')";
//             $result2 = mysqli_query($conn, $optionQuery);
    
//             // Check if option insertion was successful
//             if ($result2) {
//                 // If option insertion was successful, continue to the next option
//                 continue;
//             } else {
//                 // If option insertion failed, log the error message
//                 $error = mysqli_error($conn);
//                 error_log("Failed to insert option into database: $error");
//                 // Return the error message with more details
//                 echo json_encode(array("error" => "Failed to insert option into database: $error"));
//                 return;
//             }
  
// }
// }
// // Retrieve the entered answer from $questionData
// // Retrieve the entered answer from $questionData
// $enteredAnswer = isset($questionData['trail_mcq_answer']) ? $questionData['trail_mcq_answer'] : null;

// // Check if the entered answer is provided and not empty
// if (!isset($questionData['trail_mcq_answer']) || empty($questionData['trail_mcq_answer'])) {
//     echo json_encode(array("error" => "Entered answer is missing. Please make sure to provide an answer."));
//     return;
// }

// // Check if the entered answer is provided
// // // Check if the entered answer is provided and not empty
// if ($enteredAnswer === null || $enteredAnswer === '') {
//     // $error = mysqli_error($enteredAnswer);
//     echo json_encode(array("error" => "Entered answer is missing"));
//     return;
// }


// // If the entered answer is valid and provided, proceed with further validation
// // Check if the entered answer matches any MCQ option
// if (!in_array($enteredAnswer, $options)) {
//     // If the entered answer is not in the options array, return an error
//     echo json_encode(array("error" => "Entered answer does not match any MCQ option"));
//     return;
// }

// // If the entered answer matches an option, return success message along with the entered answer
// echo json_encode(array("success" => true, "enteredAnswer" => $enteredAnswer));

// $optionIdQuery = "SELECT trial_mcq_id FROM edu_trial_mcq_options WHERE trial_mcq_num = '$questionId' AND trial_mcq_answer = '$enteredAnswer'";
// $optionIdResult = mysqli_query($conn, $optionIdQuery);

// if (!$optionIdResult) {
//     $error = mysqli_error($conn);
//     echo json_encode(array("error" => "Failed to fetch option ID from database: $error"));
//     return;
// }

// // Initialize an empty array to store the fetched data
// $questions = array();

// // Check if there are rows returned by the query
// if (mysqli_num_rows($optionIdResult) > 0) {
//     // Fetch data and store it in an array
//     while ($row = mysqli_fetch_assoc($optionIdResult)) {
//         echo "trial_mcq_id: " . $row['trial_mcq_id'] . "<br>";

//         $question = array(

//             "trial_mcq_id" => $row['trial_mcq_id'],
//         );
//         $questions[] = $question;
//     }
// } else {
//     echo json_encode(array("error" => "No rows returned by the query"));
// }


// $optionIdRow = mysqli_fetch_assoc($optionIdResult);

// // Check if there is a row fetched
// if (!$optionIdRow) {
//     // Handle the case where no row is fetched
//     echo json_encode(array("error" => "No trial_mcq_id returned by the query."));
//     return;
// }

// // Fetch the trial_mcq_id from the result
// $optionId = $optionIdRow['trial_mcq_id'];

// // Insert the trial_mcq_id into edu_trial_mcq_questions table
// // $trialnumQuery = "INSERT INTO edu_trial_mcq_questions (trial_mcq_id) VALUES ('$optionId')";
// // $trialnumResult = mysqli_query($conn, $trialnumQuery);

// $trialnumQuery = "INSERT INTO edu_trial_mcq_questions (trial_mcq_id) VALUES ('$optionId')";
// $trialnumResult = mysqli_query($conn, $trialnumQuery);
// // Check if insertion of trial_mcq_questions was successful
// if (!$trialnumResult) {
//     // Handle the insertion failure
//     echo json_encode(array("error" => "Failed to insert trial_mcq_questions."));
//     return;
// }

// // Return success message
// echo json_encode(array("success" => true));
// }


function insertMCQuestionWithOptions($questionData, $options) {
    global $conn;

    // Insert the question
    $questionText = mysqli_real_escape_string($conn, $questionData['trail_mcq_question']);
    $moduleId = 10001; // Assuming mod_id is a fixed value

    $questionQuery = "INSERT INTO edu_trial_mcq_questions (mod_id, trail_mcq_question) VALUES ('1001', '$questionText')";
    $result1 = mysqli_query($conn, $questionQuery);

    if (!$result1) {
        echo json_encode(array("error" => "Failed to insert question into database"));
        return;
    }

    // Retrieve the auto-generated question_id
    $questionId = mysqli_insert_id($conn);

    // Insert options for the question
    for ($i = 0; $i < 3; $i++) {
        // Check if options array has enough items
        if (isset($options[$i])) {
            // Escape the option text
            $optionText = mysqli_real_escape_string($conn, $options[$i]);
            
            // Insert the option into the database
            $optionQuery = "INSERT INTO edu_trial_mcq_options (trial_mcq_num, trial_mcq_answer) VALUES ('$questionId', '$optionText')";
            $result2 = mysqli_query($conn, $optionQuery);

            // Check if option insertion was successful
            if ($result2) {
                // If option insertion was successful, continue to the next option
                continue;
            } else {
                // If option insertion failed, log the error message
                $error = mysqli_error($conn);
                error_log("Failed to insert option into database: $error");
                // Return the error message with more details
                echo json_encode(array("error" => "Failed to insert option into database: $error"));
                return;
            }
        }
    }

    // Retrieve the entered answer from $questionData
    $enteredAnswer = isset($questionData['trail_mcq_answer']) ? $questionData['trail_mcq_answer'] : null;

    // Check if the entered answer is provided and not empty
    if (!isset($questionData['trail_mcq_answer']) || empty($questionData['trail_mcq_answer'])) {
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

    // Fetch the trial_mcq_id from the result
    $optionIdQuery = "SELECT trial_mcq_id FROM edu_trial_mcq_options WHERE trial_mcq_num = '$questionId' AND trial_mcq_answer = '$enteredAnswer'";
    $optionIdResult = mysqli_query($conn, $optionIdQuery);

    if (!$optionIdResult) {
        // Handle the error
        $error = mysqli_error($conn);
        echo json_encode(array("error" => "Failed to fetch trial_mcq_id from database: $error"));
        return;
    }

    // Check if there is a row fetched
    if (mysqli_num_rows($optionIdResult) == 0) {
        // Handle the case where no row is fetched
        echo json_encode(array("error" => "No trial_mcq_id returned by the query."));
        return;
    }

    // Fetch the trial_mcq_id from the result
    $optionIdRow = mysqli_fetch_assoc($optionIdResult);

    // Check if there is a row fetched
    if (!$optionIdRow) {
        // Handle the case where no row is fetched
        echo json_encode(array("error" => "No trial_mcq_id returned by the query."));
        return;
    }

    // You don't need to insert trial_mcq_id into edu_trial_mcq_questions again
    // $trialnumQuery = "INSERT INTO edu_trial_mcq_questions (trial_mcq_id) VALUES ('$optionId')";
    // $trialnumResult = mysqli_query($conn, $trialnumQuery);

    // Check if insertion of trial_mcq_questions wassuccessful
    // if (!$trialnumResult) {
    //     // Handle the insertion failure
    //     echo json_encode(array("error" => "Failed to insert trial_mcq_questions."));
    //     return;
    // }

    // Return success message
    echo json_encode(array("success" => true, "trial_mcq_id" => $optionIdRow['trial_mcq_id']));
}

