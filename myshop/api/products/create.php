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
include_once '../../models/Product.php';

$database = new Database();
$client = $database->connect();
$product = new Product($client);

$data = json_decode(file_get_contents("php://input"));

//na balw na stelnei kai to sellername me th forma

$product->name = $data->name ?? null;
$product->productcode = $data->prod_code ?? null;
$product->price = $data->prod_price ?? null;
$product->dateofwithdrawal = '';
$product->sellername = $data->sellername ?? null;
$product->category = $data->prod_category ?? null;
$product->quantity = $data->quantity ?? null;
if($product->create()){
    echo json_encode(array('message'=>'You have successfully added a new product.'));
}else{
    echo json_encode(array('message'=>'There was an error in product insertion. Try again.'));
}
?>