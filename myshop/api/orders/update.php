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

$result = $order->update($data);
if($result){
    echo(json_encode(array('msg'=>'Status updated successfully')));
}else{
    header("HTTP/1.0 500 Internal Server Error");
    echo json_encode(array('msg'=>'There was an error in order updating. Try again.'));
}
?>