<?php
// error_reporting(0);
header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include('count.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == 'GET') {
    $getCounts = getCounts();
    echo json_encode($getCounts);
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod . ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}

function getCounts()
{
    $userCount = getUserCount();
    $trailModulesCount = getTrailModulesCount();
    $modulesCount = getUserCounts();

    return [
        'user_count' => $userCount,
        'trail_modules_count' => $trailModulesCount,
        'modules_count' => $modulesCount
    ];
}
?>
