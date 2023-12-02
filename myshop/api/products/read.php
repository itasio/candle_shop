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
include_once '../../models/Product.php';

$database = new Database();
$client = $database->connect();
$product = new Product($client);

$data = json_decode(file_get_contents("php://input"));

$filter = [];

if(isset($data->name)) $filter['NAME'] = $data->name;
if(isset($data->prod_code)) $filter['PRODUCTCODE'] = $data->prod_code;
if(isset($data->date)){ 
    // show products that have withdrawed later from the selected date or they are available
    // $filter['DATEOFWITHDRAWAL'] = ['$gte' =>$data->date, '$eq' => ''];
    $filter = [
        '$or' => [
            ['DATEOFWITHDRAWAL' => ['$gt' => $data->date]],
            ['DATEOFWITHDRAWAL' => ['$eq' => '']],
        ]
    ];
}
if(isset($data->status)){
    if($data->status == 'active'){
        $filter['DATEOFWITHDRAWAL'] = ['$eq' => ''];
    }elseif ($data->status == 'withdrawed') {
        $filter['DATEOFWITHDRAWAL'] = ['$ne' => ''];
    }
}
if(isset($data->sellers)) $filter['SELLERNAME'] = $data->sellers;
if(isset($data->categories)) $filter['CATEGORY'] = $data->categories;
if(isset($data->quantity)) $filter['QUANTITY'] = $data->quantity;
if(isset($data->maximum) && isset($data->minimum))
    $filter['PRICE'] = ['$gte' => (float)$data->minimum, '$lte' => (float)$data->maximum];

    
if(count($filter) > 0)
    $result = $product->read($filter);  //query all products
else
    $result = $product->read();         //query based on parameters

// if (count($result) > 0) {
//     echo json_encode($result);
// }else{
//     echo json_encode(array('message' => 'There aren\'t any products currently available in our site matching your criteria.'));
// }
echo json_encode($result);

?>