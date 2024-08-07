<?php
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include('functiontest1.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == 'DELETE') {
    // Extract the question ID from the URL parameter
    $questionId = isset($_GET['question_id']) ? $_GET['question_id'] : null;
    
    if ($questionId === null) {
        // If question ID is not provided in the URL parameter, return an error
        $data = [
            'status' => 422,
            'message' => 'Question ID is missing in the request.',
        ];
        http_response_code(422);
        echo json_encode($data);
        exit();
    }

    // Call the deleteMCQuestion function and get the response
    $deleteMCQuestion = deleteMCQuestion($questionId);
    echo $deleteMCQuestion;
} else {
    // If the request method is not DELETE, return a 405 Method Not Allowed error
    $data = [
        'status' => 405,
        'message' => $requestMethod . ' Method Not Allowed',
    ];
    http_response_code(405);
    echo json_encode($data);
}
?>