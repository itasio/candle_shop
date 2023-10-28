<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/Product.php';

$database = new Database();
$client = $database->connect();
$product = new Product($client);

$data = json_decode(file_get_contents("php://input"));

//na balw na stelnei kai to sellername me th forma

$product->name = $data->name;
$product->productcode = $data->prod_code;
$product->price = $data->prod_price;
$product->dateofwithdrawal = '';
$product->sellername = $data->sellername;
$product->category = $data->prod_category;
if($product->create()){
    echo json_encode(array('message'=>'You have successfully added a new product.'));
}else{
    echo json_encode(array('message'=>'There was an error in product insertion. Try again.'));
}
?>