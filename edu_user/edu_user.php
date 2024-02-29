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

// Update user
function updateUser($userInput, $userparams){
    global $conn;

    // Check if required parameters are set
    if(!isset($userparams['user_email'])) {
        return error422('User email is not found in URL');
    } elseif(!isset($userInput['user_pswd'])){
        return error422('Password is missing');
    } elseif(empty(trim($userInput['user_pswd']))) {
        return error422('Enter the password');
    } elseif(empty(trim($userparams['user_email']))){
        return error422('Enter the email');
    }

    // Escape user inputs to prevent SQL injection
    $user_email = mysqli_real_escape_string($conn, $userparams['user_email']);
    $user_pswd = mysqli_real_escape_string($conn, $userInput['user_pswd']);

    // Update query
    $query = "UPDATE `edu_users` SET `user_pswd` = '$user_pswd' WHERE `user_email` = '$user_email' LIMIT 1";

    // Perform the update query
    $result = mysqli_query($conn, $query);

    // Check if the query was successful
    if($result){
        $data = [
            'status' => 200,
            'message' => 'User updated Successfully',
        ];
        header("HTTP/1.0 200 Created");
        echo json_encode($data);
    } else {
        // If there was an error with the query
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
    }
}

?>
