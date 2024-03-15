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


//getFunction

function getTrailModules(){
 
    global $conn;
    
    $query =  "SELECT * FROM `edu_modules_trial`";
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
    


// //getFunction
// function getTrailModules() {
//     global $conn;

//     $query = "SELECT `mod_num`, `mod_order`, `t_num`, `mod_name`, `mod_content`, `mod_description`, `mod_specialnote`, `sl_level`, `mod_example_explanation` FROM `edu_modules_trial`";

//     $query_run = mysqli_query($conn, $query);
    
//     if ($query_run) {
//         if (mysqli_num_rows($query_run) > 0) {
//             $data = array();
//             while ($row = mysqli_fetch_assoc($query_run)) {
//                 // Check if the keys exist in the $row array before accessing them
//                 if (isset($row['topic_ans_answer']) && isset($row['topic_que_question'])) {
//                     $question = array(
//                         'topic_que_num' => $row['topic_que_num'],
//                         'topic_ans_answer' => $row['topic_ans_answer'],
//                         'topic_que_question' => $row['topic_que_question'],
//                     );
//                     $data[] = $question;
//                 }
//             }
//             header("HTTP/1.0 200 Success");
//             return json_encode($data);
//         } else {
//             $data = [
//                 'status' => 404,
//                 'message' => 'No Student Found', // corrected typo here
//             ];
//             header("HTTP/1.0 404 Not Found");
//             return json_encode($data);
//         }
//     } else {
//         $data = [
//             'status' => 500,
//             'message' => 'Internal Server Error',
//         ];
//         header("HTTP/1.0 500 Internal Server Error");
//         return json_encode($data);
//     }
// }

?>