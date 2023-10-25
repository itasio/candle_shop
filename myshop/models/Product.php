<?php

use MongoDB\Exception\Exception;

class Product{
    private $client;
    private $db = "shop1";
    private $collection_name = "products";
    private $name;
    private $productcode;
    private $price;
    private $dateofwithdrawal;
    private $sellername;
    private $category;

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function read_all(){
        try {
            $collection = $this->client->$this->db->$this->collection_name;
            $cursor = $collection->find();

            foreach ($cursor as $document) {
                echo $document['name'], "\n";
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        
    }
}
?>
