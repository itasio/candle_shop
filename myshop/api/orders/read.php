<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');


$method = $_SERVER['REQUEST_METHOD'];

if($method != 'POST'){
    header("HTTP/1.0 405 Method Not Allowed");
    exit(json_encode(array('msg'=>'Method not allowed')));
}
include_once '../../config/Database.php';
include_once '../../models/Order.php';

$database = new Database();
$client = $database->connect();
$order = new Order($client);
$data = json_decode(file_get_contents("php://input"));

if(isset($data->user_username)){
    $filter['USER_USERNAME'] = $data->user_username;
    $result = $order->read($filter);
    echo json_encode($result);
    // if (count($result) > 0) {
    //     echo json_encode($result);
    // }else{
    //     echo json_encode(array('message' => 'There aren\'t any orders for the requested user in our site.'));
    // }
}else{
    echo json_encode(array());
}
?>