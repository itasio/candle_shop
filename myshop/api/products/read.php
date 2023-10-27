<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Product.php';

$database = new Database();
$client = $database->connect();
$product = new Product($client);
$result = $product->read_all();

if (count($result) > 0) {
    echo json_encode($result);
}else{
    echo json_encode(array('message' => 'There aren\'t any products currently available in our site.'));
}


?>