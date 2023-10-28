<?php

use MongoDB\Exception\Exception;

class Product{
    private $client;
    // private $db = "shop1";
    // private $collection_name = "products";
    public  $name;
    public  $productcode;
    public  $price;
    public  $dateofwithdrawal;
    public  $sellername;
    public  $category;

    public function __construct($client)
    {
        $this->client = $client;
    }

      /**
     * Fuction that creates a document in the products collection.
     * Returns true if the document was added successfully or false on failure.
     * 
     * @return boolean
     */
    public function create(){
        try {
            $collection = $this->client->shop1->products;
            $insertOneResult = $collection->insertOne([
                'NAME' => $this->name,
                'PRODUCTCODE' => $this->productcode,
                'PRICE' => $this->price,
                'DATEOFWITHDRAWAL' => $this->dateofwithdrawal,
                'SELLERNAME' => $this->sellername,
                'CATEGORY' => $this->category
            ]);
            return $insertOneResult->isAcknowledged();
 
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

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

    /**
     * Function that queries the products based on the parameter passed as arguments and returns an array with all the product documents or 
     * an empty array if there aren't any. 
     * 
     * @return array
     */
    public function read_by_seller($param){

    } 
}
?>
