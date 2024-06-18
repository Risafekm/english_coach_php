
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
?>