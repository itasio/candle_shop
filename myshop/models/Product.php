<?php

use MongoDB\Exception\Exception;

class Product{
    private $client;
    // private $db = "shop1";
    // private $collection_name = "products";
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

    /**
     * Fuction checks for product documents in the database and returns an array with all the product documents or 
     * an empty array if there aren't any.
     * 
     * @return array
     */
    public function read_all(){
        try {
            $collection = $this->client->shop1->products;
            $cursor = $collection->find();
            $prod_arr = array();

            foreach ($cursor as $document) {
                $prod_item = array(
                    'name' => $document['NAME'],
                    'productcode' => $document['PRODUCTCODE'],
                    'price' => $document['PRICE'],
                    'dateofwithdrawal' => $document['DATEOFWITHDRAWAL'],
                    'sellername' => $document['SELLERNAME'],
                    'category' => $document['CATEGORY']
                );

                // Push to post array
                array_push($prod_arr, $prod_item);
            }

            return $prod_arr;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        
    }
}
?>
