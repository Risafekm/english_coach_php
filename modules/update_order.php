<?php
require 'connection.php';

function reorderModules($updatedModules) {
    global $conn;

    try {
        $conn->autocommit(FALSE); // Start transaction

        foreach ($updatedModules as $module) {
            $modNum = mysqli_real_escape_string($conn, $module['mod_num']);
            $modOrder = mysqli_real_escape_string($conn, $module['mod_order']);

            $query = "UPDATE `edu_modules` SET `mod_order` = '$modOrder' WHERE `mod_num` = '$modNum'";

            $result = mysqli_query($conn, $query);

            if (!$result) {
                throw new Exception(mysqli_error($conn));
            }
        }

        $conn->commit(); // Commit transaction
        $data = [
            'status' => 200,
            'message' => 'Modules reordered successfully',
        ];
        header("HTTP/1.0 200 OK");
        echo json_encode($data);
    } catch (Exception $e) {
        $conn->rollback(); // Rollback transaction in case of error
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error: ' . $e->getMessage(),
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
    } finally {
        $conn->autocommit(TRUE); // Restore autocommit to true
    }
}

// Usage example
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $requestData = json_decode(file_get_contents('php://input'), true);

    if (isset($requestData['modules'])) {
        reorderModules($requestData['modules']);
    } else {
        $data = [
            'status' => 422,
            'message' => 'Invalid request format',
        ];
        header("HTTP/1.0 422 Unprocessable Entity");
        echo json_encode($data);
    }
} else {
    $data = [
        'status' => 405,
        'message' => 'Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>
