<?php

use MongoDB\Exception\Exception;

class Order{
    private $client;
    //private $db = "Orders";
    private $collection;
    public $status;
    public $total_price;
    public $user_username;
    public $products;

    public function __construct($client)
    {
        $this->client = $client;
        try {
            $this->collection = $this->client->Orders->order_info;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Checks for orders of a user based on the username $filter and returns an array of them,
     * or an empty array if no orders exist
     * 
     * @param string $filter the user username 
     * @return array
     * 
     */
    public function read($filter){
        try {
            if($filter){
                $cursor = $this->collection->find($filter);
                $data = iterator_to_array($cursor);
                return $data;
            }else{
                return [];
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Creates a document in the Orders collection.
     * Returns true if the document was added successfully or false on failure.
     * 
     * @return boolean
     */
    public function create($data){
        try {
            if(isset($data->total_price) && isset($data->user_username) && isset($data->products)){
                $insertOneResult = $this->collection->insertOne([
                    'USER_USERNAME'   => $data->user_username,
                    'TOTAL_PRICE'     => $data->total_price,
                    'STATUS'          => "PENDING",
                    'PRODUCTS'        => $data->products
                ]);
                return $insertOneResult->isAcknowledged();
            }else{
                return false;
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
}

?>