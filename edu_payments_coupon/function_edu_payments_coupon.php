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

//delete 

function deleteedupaymentscoupon($edupaymentscouponparams) {
    global $conn;

    if (!isset($edupaymentscouponparams['coupon_id'])) {
        return error422("id not found ");
    }

    $coupon_id = mysqli_real_escape_string($conn, $edupaymentscouponparams['coupon_id']);

    // Start a transaction to ensure both queries are executed or none
    mysqli_begin_transaction($conn);

    // Delete from edu_module_exercises table
    $query1 = "DELETE FROM `edu_payments_coupon` WHERE `coupon_id` = $coupon_id LIMIT 1";
    $result1 = mysqli_query($conn, $query1);


    if ($result1) {
        // Commit the transaction if both queries are successful
        mysqli_commit($conn);

        $data = [
            'status' => 200,
            'message' => 'Values deleted successfully',
        ];
        header("HTTP/1.0 200 success");
        return json_encode($data);
    } else {
        // Rollback the transaction if any query fails
        mysqli_rollback($conn);

        $data = [
            'status' => 404,
            'message' => 'Values not found or deletion failed',
        ];
        header("HTTP/1.0 404 Not found");
        return json_encode($data);
    }
}


