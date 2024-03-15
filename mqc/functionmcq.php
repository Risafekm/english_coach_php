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

function insertQuestionsAndOptions($questions) {
    global $conn;

    // Iterate over each question
    foreach ($questions as $questionData) {
        $question = mysqli_real_escape_string($conn, $questionData['question']);
        $correctAnswer = mysqli_real_escape_string($conn, $questionData['correct_answer']);

        // Insert the question
        $questionQuery = "INSERT INTO edu_trial_mcq_questions (trail_mcq_question, trail_mcq_correct_answer) VALUES ('$question', '$correctAnswer')";
        mysqli_query($conn, $questionQuery);

        // Get the ID of the newly inserted question
        $question_id = mysqli_insert_id($conn);

        $options = $questionData['options'];
        // Insert options for the question
        foreach ($options as $option) {
            $option = mysqli_real_escape_string($conn, $option);
            $optionQuery = "INSERT INTO edu_trial_mcq_options (trial_mcq_num, trial_mcq_answer) VALUES ($question_id, '$option')";
            mysqli_query($conn, $optionQuery);
        }
    }
}

// Example data for questions, options, and correct answers
$questions = array(
    array(
        'question' => 'Question 1?',
        'options' => array('Option 1', 'Option 2', 'Option 3', 'Option 4'),
        'correct_answer' => 'Option 2'
    ),
    array(
        'question' => 'Question 2?',
        'options' => array('Option A', 'Option B', 'Option C', 'Option D'),
        'correct_answer' => 'Option B'
    )
);




// Function to fetch questions and options from the database
function getQuestionsAndOptions() {
    global $conn;

    // Perform the SQL query to fetch questions, options, and correct answers
    $query = "SELECT 
                edu_trial_mcq_questions.trail_mcq_num,
                edu_trial_mcq_questions.trail_mcq_question, 
                JSON_ARRAYAGG(edu_trial_mcq_options.trial_mcq_answer) AS options,
                (SELECT edu_trial_mcq_options.trial_mcq_answer  FROM 
                edu_trial_mcq_options WHERE
                edu_trial_mcq_options.trial_mcq_id = edu_trial_mcq_questions.trial_mcq_id) AS mcq_answer
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



?>