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

// Call the function to fetch questions and options
// getQuestionsAndOptions();

?>
