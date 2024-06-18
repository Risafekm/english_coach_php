<?php

require 'connection.php';

header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$inputData = json_decode(file_get_contents("php://input"), true);

if ($inputData) {
  try {
    // Iterate through the received array of modules and update their orders in the database
    foreach ($inputData as $index => $module) {
      $modNum = mysqli_real_escape_string($conn, $module['mod_num']);
      $modOrder = mysqli_real_escape_string($conn, $index +1); // Assuming 1-based indexing
      $query = "UPDATE `edu_modules` SET `mod_order` ='$modOrder' WHERE `mod_num` = '$modNum'";
      $result = mysqli_query($conn, $query);
      if (!$result) {
        throw new Exception(mysqli_error($conn));
      }
    }
    // Return success response
    $data = [
      'status' => 200,
      'message' => 'Modules reordered successfully',
    ];
    header("HTTP/1.0 200 OK");
    echo json_encode($data);
  } catch (Exception $e) {
    $data = [
      'status' => 500,
      'message' => 'Internal Server Error: ' . $e->getMessage(),
    ];
    header("HTTP/1.0 500 Internal Server Error");
    echo json_encode($data);
  }
} else {
  $data = [
    'status' => 422,
    'message' => 'Invalid input data',
  ];
  header("HTTP/1.0 422 Unprocessable Entity");
  echo json_encode($data);
}

?>