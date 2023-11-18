<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] != 'GET'){
    header("HTTP/1.0 405 Method Not Allowed");
    exit(json_encode(array('msg'=>'Method not allowed')));
}
include_once '../../config/Database.php';
include_once '../../models/Product.php';

$database = new Database();
$client = $database->connect();
$product = new Product($client);


$result = $product->read();


if (count($result) > 0) {
    echo json_encode($result);
}else{
    echo json_encode(array('message' => 'There aren\'t any products currently available in our site.'));
}


?>