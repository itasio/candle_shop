<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if($method != 'GET'){
    header("HTTP/1.0 405 Method Not Allowed");
    exit(json_encode(array('msg'=>'Method not allowed')));
}
include_once '../../config/Database.php';
include_once '../../models/Product.php';

$database = new Database();
$client = $database->connect();
$product = new Product($client);

if(isset($_GET['q']) && $_GET['q'] == 'category')
    $param = "CATEGORY";
elseif(isset($_GET['q']) && $_GET['q'] == 'productseller')
    $param = "SELLERNAME";
else
    exit(json_encode([]));

$result = $product->find_distinct($param);
echo json_encode($result);
?>