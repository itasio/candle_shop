<?php

use MongoDB\Exception\Exception;

class Product{
    private $client;
    //private $db = "Products";
    private $collection;
    public  $name;
    public  $productcode;
    public  $price;
    public  $dateofwithdrawal;
    public  $quantity;
    public  $sellername;
    public  $category;

    public function __construct($client)
    {
        $this->client = $client;
        try {
            $this->collection = $this->client->Products->product_info;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

      /**
     * Fuction that creates a document in the products collection.
     * Returns true if the document was added successfully or false on failure.
     * 
     * @return boolean
     */
    public function create(){
        try {
            $insertOneResult = $this->collection->insertOne([
                'NAME'              => $this->name,
                'PRODUCTCODE'       => $this->productcode,
                'PRICE'             => $this->price,
                'DATEOFWITHDRAWAL'  => $this->dateofwithdrawal,
                'QUANTITY'          => $this->quantity,
                'SELLERNAME'        => $this->sellername,
                'CATEGORY'          => $this->category
            ]);
            return $insertOneResult->isAcknowledged();
 
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }

    /**
     * Checks for product documents in the database and returns an array
     * with all the product documents matching the filter and options criteria or 
     * an empty array if there aren't any.
     * 
     * @param array $filter the query criteria(Optional)
     * @param array $options the desired options e.g. limit, sort etc.(Optional)
     * 
     * @return array
     */
    public function read($filter = null, $options = null){
        try {
            if ($filter && $options) {
                $cursor = $this->collection->find($filter, $options);
            }elseif ($filter) {
                $cursor = $this->collection->find($filter);
            }
            elseif ($options) {
                $cursor = $this->collection->find($options);
            }else{
                $cursor = $this->collection->find();
            }
            // $prod_arr = array();
            // foreach ($cursor as $document) {
            //     $prod_item = array(
            //         'name'              => $document['NAME'],
            //         'productcode'       => $document['PRODUCTCODE'],
            //         'price'             => $document['PRICE'],
            //         'dateofwithdrawal'  => $document['DATEOFWITHDRAWAL'],
            //         'quantity'          => $document['QUANTITY'],
            //         'sellername'        => $document['SELLERNAME'],
            //         'category'          => $document['CATEGORY']
            //     );

            //     // Push to post array
            //     array_push($prod_arr, $prod_item);
            // }

            $data = iterator_to_array($cursor);
            return $data;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        
    }

}
?>
