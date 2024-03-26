<?php
require 'connection.php';

function error422($message){
    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 Unprocessable Entity");
    echo json_encode($data);
    exit();
}


// Function to fetch table count from the database
function getCount() {
    global $conn;
    try {
        // Perform the SQL query to fetch the count of rows in the table
        $query = "SELECT COUNT(mod_num) as count FROM edu_modules";
        
        // Execute the query
        $result = $conn->query($query);

        // Check if query was successful
        if ($result) {
            // Fetch data and store it in an array
            $row = $result->fetch_assoc();
            if (isset($row['count'])) {
                // Access the "count" key if it exists
                $tableCount = $row['count'];
            }
            // Return the table count
            return $tableCount;
        } else {
            // Handle query error
            throw new Exception("Failed to execute query: $query");
        }
    } catch (PDOException $e) {
        // Handle PDOException (SQLite errors)
        error_log("SQLite Error: " . $e->getMessage());
        return false;
    }
}
?>