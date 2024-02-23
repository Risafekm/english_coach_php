<?php
// error_reporting(0);
header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
include 'connection.php';

// Get the JSON array from the request body
$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    // Loop through the array and update the order in the database
    foreach ($data as $index => $item) {
        $mod_order = $item['mod_order'];
        $mod_num = $item['mod_num'];

        // Update the order in the database
        $updateQuery = "UPDATE edu_modules SET mod_order = $mod_order WHERE mod_num = $mod_num";

        if ($conn->query($updateQuery) === TRUE) {
            // Successfully updated order for the record with mod_num = $mod_num
        } else {
            // Failed to update order
            echo "Error updating order: " . $conn->error;
        }
    }

    // Close the database connection
    $conn->close();

    // Send a response back to the client
    http_response_code(200);
    echo json_encode(array("message" => "Order updated successfully"));
} else {
    // Invalid or empty data
    http_response_code(400);
    echo json_encode(array("message" => "Invalid or empty data"));
}
?>
