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

function deletePreliminaryTest2($preliminaryTest2params){
 
    global $conn;

    if(!isset($preliminaryTest2params['prelim_trans_ques_num'])){
        return error422("id not found ");
    }

    $prelimNum = mysqli_real_escape_string($conn, $preliminaryTest2params['prelim_trans_ques_num']);
    $query = "DELETE FROM `edu_preliminary_trans_questions` WHERE `prelim_trans_ques_num` = $prelimNum LIMIT 1";
    $result = mysqli_query($conn ,$query);

    if($result){

        $data = [
            'status' => 200,
            'message' => 'Student deleted successfully',
        ];
        header("HTTP/1.0 200  success");
        return json_encode($data);

    }else{
        $data = [
            'status' => 404,
            'message' => 'Student not found',
        ];
        header("HTTP/1.0 400  Not found");
        return json_encode($data);
    }


}

//Update 

function updatePreliminaryTest2List($preliminaryInput, $preliminaryTest2params)
{
    global $conn;

    if (!isset($preliminaryTest2params['prelim_trans_ques_num'])) {
        return error422('id not found');
    } 

    $prelimNum = mysqli_real_escape_string($conn, $preliminaryTest2params['prelim_trans_ques_num']);
    $prelim = mysqli_real_escape_string($conn, $preliminaryInput['prelim_trans_question']);
    

    if (empty(trim($prelim))) {
        return error422('Enter the prelim_trans_question');
    }else {

        $query = "UPDATE `edu_preliminary_trans_questions` SET `prelim_trans_question` = '$prelim', `prelim_trans_ques_num` ='$prelimNum'  WHERE `prelim_trans_ques_num` = '$prelimNum'";
        $result = mysqli_query($conn, $query);

        if ($result) {

            $data =[
                'status' => 200,
                'message' => 'updated successfull',
            ];
            header("HTTP/1.0 200 Created");
            echo json_encode($data);

        } else {
            $data = [
                'status' => 500,
                'message' => 'Internal Server Error',
            ];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data);
        }
    }
}


//postFunction

function storePreliminaryTest2List($preliminaryInput){

    global $conn;

    $prelim = mysqli_real_escape_string($conn, $preliminaryInput['prelim_trans_question']);
    $tnum = mysqli_real_escape_string($conn, $preliminaryInput['t_num']);
   
    

    if(empty(trim($prelim))){
     return error422('Enter the prelim_trans_question');
    }
    else{

       $query = "INSERT INTO `edu_preliminary_trans_questions`(`prelim_trans_question`, `t_num`) VALUES ('$prelim','1')";
       $result = mysqli_query($conn, $query);

     if($result){
  
        $data = [
            'status' => 201,
            'message' => 'edu_preliminary_trans_questions Created Successfully',
        ];
        header("HTTP/1.0 201 Created");
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


//getFunction

function getPreliminaryTest2List(){
 
global $conn;

$query = "SELECT * FROM `edu_preliminary_trans_questions`";
$query_run = mysqli_query($conn ,$query);

if($query_run){

if(mysqli_num_rows($query_run) > 0){

$res = mysqli_fetch_all($query_run,MYSQLI_ASSOC);

  $data = $res;
    
header("HTTP/1.0 200  Success");
return json_encode($data);

}else{
    $data = [
        'status' => 404,
        'message' => 'No Student Found',
    ];
    header("HTTP/1.0 404  No Student Found");
    return json_encode($data);
}

}else{
    $data = [
        'status' => 500,
        'message' => 'Internal Server Error',
    ];
    header("HTTP/1.0 500  Internal Server Error");
    return json_encode($data);
}

}

//1 to 1 data fetching

function getPreliminaryTest2($preliminaryTest2params){

global $conn;


if($preliminaryTest2params['prelim_trans_ques_num'] == null){
    return error422('Enter your id');
   }

$prelimNum = mysqli_real_escape_string($conn, $preliminaryTest2params['prelim_trans_ques_num']);

$query = "SELECT * FROM `edu_preliminary_trans_questions` WHERE `prelim_trans_ques_num` = '$prelimNum' LIMIT 1";
$result = mysqli_query($conn,$query);

if($result){

 if(mysqli_num_rows($result) > 0){
 
    $res = mysqli_fetch_assoc($result);
    $data =$res;
    header("HTTP/1.0 200  Success");
    return json_encode($data);


 }else{
 $data = [
        'status' => 404,
        'message' => 'No edu_preliminary_trans_questions Found',
    ];
    header("HTTP/1.0 404  Not found");
    return json_encode($data);
 }

}else{
    $data = [
        'status' => 500,
        'message' => 'Internal Server Error',
    ];
    header("HTTP/1.0 500  Internal Server Error");
    return json_encode($data);
}

}

?>