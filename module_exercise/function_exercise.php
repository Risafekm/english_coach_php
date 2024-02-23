<?php

require 'connection.php';

// Get method

function getModuleExerciseList() {
    global $conn;
    $query = "SELECT * FROM edu_module_exercises";
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
            'message' => 'No module exercise Found',
        ];
        header("HTTP/1.0 404  No module exercise Found");
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

 // 1 to 1 fetching 

 function getModuleExercise($getModuleExerciseParams){

    global $conn;
    
    
    if($getModuleExerciseParams['exe_num'] == null){
        return error422('Enter your number');
       }
    
    $exeNum = mysqli_real_escape_string($conn, $getModuleExerciseParams['exe_num']);
    
    $query = "SELECT * FROM edu_module_exercises WHERE exe_num = '$exeNum' LIMIT 1";
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
            'message' => 'No Exercise Number Found',
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

   // Post method
   
   function storeModuleExercise($moduleExerciseInput) {
    global $conn;

    $exeNum = mysqli_real_escape_string($conn,$moduleExerciseInput['exe_num']);
    $modNum = mysqli_real_escape_string($conn,$moduleExerciseInput['mod_num']);
    $exeQuestion = mysqli_real_escape_string($conn,$moduleExerciseInput['exe_question']);
    $exeAnswer = mysqli_real_escape_string($conn,$moduleExerciseInput['exe_answer']);
    $exeSentenceRule = mysqli_real_escape_string($conn,$moduleExerciseInput['exe_sentence_rule']);

    if(empty(trim($exeNum))){

        return error422('enter exercise number');

    }elseif(empty(trim($modNum))){
        return error422('enter mod number');

    }elseif(empty(trim($exeQuestion))){

        return error422('enter exercise question');
    } elseif(empty(trim($exeAnswer))){
        return error422('enter exercise answer');
    }
    else {
        $query = "INSERT INTO `edu_module_exercises`(`exe_num`,`mod_num`,`exe_question`,`exe_answer`,`exe_sentence_rule`) VALUES('$exeNum','$modNum','$exeQuestion','$exeAnswer','$exeSentenceRule')";
        $result = mysqli_query($conn,$query);

        if($result){

            $data = [
                'status' => 201,
                'message' => 'modules Exercise created successfully',
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

//Update method

function updateModuleExercise($moduleExerciseInput,$moduleExerciseParams){

    global $conn;

    if(!isset($moduleExerciseParams['exe_num'])) {
        return error422('Exercise number is not found in url');
    } elseif($moduleExerciseParams['exe_num']== null){
        return error422('Enter the number');
    }

    $exeNum = mysqli_real_escape_string($conn,$moduleExerciseParams['exe_num']);
    $modNum = mysqli_real_escape_string($conn,$moduleExerciseInput['mod_num']);
    $exeQuestion = mysqli_real_escape_string($conn,$moduleExerciseInput['exe_question']);
    $exeAnswer = mysqli_real_escape_string($conn,$moduleExerciseInput['exe_answer']);
    $exeSentenceRule = mysqli_real_escape_string($conn,$moduleExerciseInput['exe_sentence_rule']);
   


    if(empty(trim($exeNum))){

        return error422('enter exe number');

    }elseif(empty(trim($modNum))){
        return error422('enter module number');

    }elseif(empty(trim($exeQuestion))){
        return error422('enter exercise question');

    }elseif(empty(trim($exeAnswer))){
        return error422('enter exercise answer');

    }elseif(empty(trim($exeSentenceRule))){

        return error422('enter sentence');
    } 
    else {

        $query = "UPDATE `edu_module_exercises` SET `exe_num` = '$exeNum', `mod_num` ='$modNum', `exe_question` = '$exeQuestion', `exe_answer` = '$exeAnswer', `exe_sentence_rule` ='$exeSentenceRule' WHERE `exe_num` = $exeNum LIMIT 1";

        $result = mysqli_query($conn, $query);

        if($result){
  
            $data = [
                'status' => 200,
                'message' => 'modules Exercise updated Successfully',
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

    //Delete method

    function deleteModuleExercise($moduleExerciseParams) {

        global $conn;

        if(!isset($moduleExerciseParams['exe_num'])){
            return error422('module Exercise number is not found in URL');
        }elseif($moduleExerciseParams['exe_num']== null) {
            return error422('Enter the module exercise number');
        }
        $exeNum = mysqli_real_escape_string($conn,$moduleExerciseParams['exe_num']);

        $query = "DELETE FROM `edu_module_exercises` WHERE `exe_num`=$exeNum";
        $result = mysqli_query($conn,$query);

        if($result) {
            $data = [
                'status' => 200,
                'message' => 'modules exercise deleted Successfully',
            ];
            header("HTTP/1.0 200 ok");
            return json_encode($data);

        } else {
            $data = [
                'status' => 404,
                'message' => 'module not found',
            ];
            header("HTTP/1.0 404  not found");
            return json_encode($data);
        }

    }
?>