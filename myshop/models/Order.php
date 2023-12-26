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
     * 
     * 
     * @return id of the created placed order, false otherwise
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
                $order_id =  $insertOneResult->getInsertedId();
                
                // $order_id = $result["\$oid"];
                if($order_id !== null){
                    //inserted successfully in db
                    //push msg to kafka
                    if($this->send_Kafka_msg($order_id->__toString(), $data)){
                        //echo "Sent to Kafka successfully";
                    }else{
                        //echo "Problem in sending to Kafka";
                    }
                }
                return $order_id;
            }else{
                return false;
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Produce messages to kafka. Returns true if success, false otherwise
     * 
     * @return boolean
     */
    function send_Kafka_msg($order_id, $order_info){
        require_once dirname(__DIR__) . '/kafka_connect.php';


        $producer = new RdKafka\Producer($conf);

        $topic = $producer->newTopic("testtt");

        $msg = new stdClass();
        $msg->id = $order_id;
        $msg->products = $order_info->products;

        $payload = json_encode($msg);
        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $payload);
        $producer->poll(0);
        for ($flushRetries = 0; $flushRetries < 3; $flushRetries++) {
            //try to flush up to 3 times
            $status = $producer->flush(10000);
            if (RD_KAFKA_RESP_ERR_NO_ERROR === $status)
                return true;
        }
        if (RD_KAFKA_RESP_ERR_NO_ERROR !== $status)
            return false;
    }
}

?>