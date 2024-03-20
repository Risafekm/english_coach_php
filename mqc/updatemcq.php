<?php
// Use require_once or include_once
// error_reporting(0);

include('functionmcq.php');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == 'PUT') {
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (empty($inputData)) {
        $data = [
            'status' => 400,
            'message' => 'Empty request data',
        ];
        header("HTTP/1.0 400 Bad Request");
        echo json_encode($data);
        exit;
    }

    // Assuming the structure of input data is as follows:
    // $inputData = ['questionData' => [...], 'options' => [...], 'questionId' => ...];

    if (!isset($inputData['questionData']) || !isset($inputData['options']) || !isset($inputData['questionId'])) {
        $data = [
            'status' => 400,
            'message' => 'Invalid request data structure',
        ];
        header("HTTP/1.0 400 Bad Request");
        echo json_encode($data);
        exit;
    }

    // Call updateMCQuestionWithOptions function with appropriate parameters
    $updateMCQuestionWithOptions = updateMCQuestionWithOptions($inputData['questionId'], $inputData['questionData'], $inputData['options']);
    echo $updateMCQuestionWithOptions;
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . 'Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method not allowed");
    echo json_encode($data);
}
?>
