<?php

use MongoDB\Exception\Exception;

class Database{
    private $client;
    

    public function connect() {
        $this->client = null;
        try {
            
            require_once __DIR__ . '/vendor/autoload.php';
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/config');  
            $dotenv->load();        //load credentials from .env file
            $mdbserver = "mongo:27017";
            // $client = new MongoDB\Client("mongodb://root:1234@mongo:27017/");
            $client = new MongoDB\Client("mongodb://".$_ENV['MDB_USER'].":".$_ENV['MDB_PASS']."@".$mdbserver."/");

        } catch (\Exception $e) {
            echo("MSG_CLIENT_FAIL");
        }
        return $this->$client;
    }
}



// $collection = $this->$client->shop1->products;
// $result = $collection->insertOne( [ 'NAME' => 'rabbit', 'PRODUCTCODE' => '002','PRICE' => '5.0','DATEOFWITHDRAWAL' => '','SELLERNAME' => 'SELLER3','CATEGORY' => 'ANIMALS', ] );
// echo "Inserted with Object ID '{$result->getInsertedId()}'";
?>