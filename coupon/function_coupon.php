
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


//update
function updateCoupon($couponInput, $couponparams){

    global $conn;

    if(!isset($couponparams['coupon_id'])) {
        return error422('module number is not found in url');
    } elseif($couponparams['coupon_id']== null){
        return error422('Enter the number');
    }

 
    $coupon_id = mysqli_real_escape_string($conn, $couponInput['coupon_id']);
    $coupon_name = mysqli_real_escape_string($conn, $couponInput['coupon_name']);
    $coupon_reduction = mysqli_real_escape_string($conn, $couponInput['coupon_reduction']);
    $coupon_count = mysqli_real_escape_string($conn, $couponInput['coupon_count']);



    if(empty(trim($coupon_name))){
        return error422('enter answer');

    }elseif(empty(trim($coupon_reduction))){
        return error422('enter question');

    }elseif(empty(trim($coupon_count))){
        return error422('enter question');

    }
    else {

        $query = "UPDATE `edu_payments_coupon` SET `coupon_name` = '$coupon_name', `coupon_name` = '$coupon_name',`coupon_count` = '$coupon_count' WHERE `coupon_id` = '$coupon_id' LIMIT 1";

        $result = mysqli_query($conn, $query);

        if($result){
  
            $data = [
                'status' => 200,
                'message' => 'modules updated Successfully',
            ];
            header("HTTP/1.0 200 Created");
            echo json_encode($data);
             
    
         }else{
            $data = [
                'status' => 500,
                'message' => 'Internal Server Error',
            ];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data);
         }
    
        }
    }

//post

function storecoupon($couponInput) {
    global $conn;

    $couponname = mysqli_real_escape_string($conn,$couponInput['coupon_name']);
    $couponreduction = mysqli_real_escape_string($conn,$couponInput['coupon_reduction']);
    $couponcount = mysqli_real_escape_string($conn,$couponInput['coupon_count']);


    if(empty(trim($couponname))){

        return error422('enter coupon name');

    }elseif(empty(trim($couponreduction))){
        return error422('enter coupon reduction');

    }elseif(empty(trim($couponcount))){

        return error422('enter coupon count');
    
    }
    else {
        $query = "INSERT INTO edu_payments_coupon(coupon_name,coupon_reduction, coupon_count) VALUES ('$couponname','$couponreduction','$couponcount')";

        $result = mysqli_query($conn,$query);

        if($result){

            $data = [
                'status' => 201,
                'message' => 'Coupon created successfully',
            ];
            header("HTTP/1.0 201  created");
            return json_encode($data);

        }else {
            $data = [
                'status' => 500,
                'message' => 'Internal Server Error',
            ];
            header("HTTP/1.0 500  Internal Server Error");
            return json_encode($data);
        }
    }


}
//get

function getCouponList() {
    global $conn;

    $query = "SELECT * FROM `edu_payments_coupon`";
    $query_run = mysqli_query($conn,$query);

    if($query_run){
        if(mysqli_num_rows($query_run) > 0){
        
            $res = mysqli_fetch_all($query_run,MYSQLI_ASSOC);

            // Return a JSON array of Customer objects
            $data = array();
            foreach ($res as $row) {
                $customer = array(
                    'coupon_id' => $row['coupon_id'],
                    'coupon_name' => $row['coupon_name'],
                    'coupon_reduction' => $row['coupon_reduction'],
                    'coupon_count' => $row['coupon_count'],
                );
                array_push($data, $customer);
            }
            header("Content-Type: application/json");
            return json_encode($data);
            
        }else{
            $data = array(
                'status' => 404,
                'message' => 'No module Found',
            );
            header("HTTP/1.0 404  No module Found");
            return json_encode($data);
        }
        
    }else{
        $data = array(
            'status' => 500,
            'message' => 'Internal Server Error',
        );
        header("HTTP/1.0 500  Internal Server Error");
        return json_encode($data);
    }
}

?>