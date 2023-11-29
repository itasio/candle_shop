<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

if($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("HTTP/1.0 405 Method Not Allowed");
    exit(json_encode(array('msg'=>'Method not allowed')));
}
include_once '../../config/Database.php';
include_once '../../models/Order.php';

$database = new Database();
$client = $database->connect();
$order = new Order($client);

$data = json_decode(file_get_contents("php://input"));

if($order->create($data)){
    echo json_encode(array('msg'=>'You have successfully created a new order.'));
}else{
    echo json_encode(array('msg'=>'There was an error in order placement. Try again.'));
}

?>