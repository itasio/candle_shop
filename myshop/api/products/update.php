<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PATCH');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');


$method = $_SERVER['REQUEST_METHOD'];

if($method != 'PATCH'){
    header("HTTP/1.0 405 Method Not Allowed");
    exit(json_encode(array('msg'=>'Method not allowed')));
}
include_once '../../config/Database.php';
include_once '../../models/Product.php';

$database = new Database();
$client = $database->connect();
$product = new Product($client);


$data = json_decode(file_get_contents("php://input"));
// $response = $product->update($data);
if($product->update($data)){
    echo(json_encode(array('msg'=>'Info updated successfully')));
}else{
    header("HTTP/1.0 400 Bad Request");
    echo(json_encode(array('msg'=>'An error occured. Please try again')));
}

?>