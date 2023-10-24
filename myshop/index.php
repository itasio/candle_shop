<?php
// require 'vendor/autoload.php'; // include Composer's autoloader

require_once __DIR__ . '/vendor/autoload.php';

// $m = new MongoClient();
$client = new MongoDB\Client("mongodb://root:1234@mongo:27017/");
echo 'hi';
$collection = $client->shop1->products;
$result = $collection->insertOne( [ 'NAME' => 'rabbit', 'PRODUCTCODE' => '002','PRICE' => '5.0','DATEOFWITHDRAWAL' => '','SELLERNAME' => 'SELLER3','CATEGORY' => 'ANIMALS', ] );
echo "Inserted with Object ID '{$result->getInsertedId()}'";
?>