<?php
/**
 * This script will be executed as a cron job every minute,
 * in order to periodically consume messages from kafka.
 * It will be the middleman between orders_topic (messages pushed from orders API)
 * and products_topic(messages pushed from products API)
 * 
 * The sequence is
 * 1. User places order
 * 2. Call to Orders API, push to kafka orders_topic
 * 3. cron job consumes msg,
 * 4. calls to Products API to check availability and update quantity,
 * 5. calls to Orders API to update order status
 */



/**
 * Consume messages form orders topic
 *//*
require_once "kafka_connect.php";
// Configure the group.id. All consumer with the same group.id will consume
// different partitions.
$conf->set('group.id', 'myConsumerGroup');

// Set where to start consuming messages when there is no initial offset in
// offset store or the desired offset is out of range.
// 'earliest': start from the beginning
$conf->set('auto.offset.reset', 'earliest');

// Emit EOF event when reaching the end of a partition
$conf->set('enable.partition.eof', 'true');

$consumer = new RdKafka\KafkaConsumer($conf);

// Subscribe to topic 'test'
$consumer->subscribe(['testtt']);

//read up to 40 messages i.e. 40 orders max per cron job
// for ($i=0; $i < 40; $i++) { 
    $message = $consumer->consume(1000);
    switch ($message->err) {
        case RD_KAFKA_RESP_ERR_NO_ERROR:
            $order = json_decode($message->payload);
            $order_id = $order->id;
            echo "New order with id: ".$order_id."\n";
            
            foreach($order->products as $product){
                $product_id =array();           //product ids of each order
                $quantity_req =array();         //array with prod_id => quantity_requested
                $quantity_remain = array();     //array with prod_id => quantity_remain

                $amount = $product->amount;
                $prod_id = $product->PRODUCT_ID;
                $product_id[] = $prod_id;
                $quantity_req[$prod_id] = $amount;
            }
            break;
        case RD_KAFKA_RESP_ERR__PARTITION_EOF:
            echo "No more messages; will wait for more\n";
            break;
        case RD_KAFKA_RESP_ERR__TIMED_OUT:
            echo "Timed out\n";
            break;
        default:
            throw new \Exception($message->errstr(), $message->err);
            break;
    }
// }




// $product_id[] = '65567a231155ad8c9675f2ed'; 
// $product_id[] = '65567a931155ad8c9675f2ef'; 

// $quantity_req['65567a231155ad8c9675f2ed'] =  1;
// $quantity_req['65567a931155ad8c9675f2ef'] =  2;


//call to Products API to read order's products info

$posted_array = json_encode(['pid' => $product_id]);

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://php-apache-environment:80/api/products/read.php',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => $posted_array,
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));



$response = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);
if($httpcode != 200){
    exit("An error occured while requesting products info.". curl_error($curl));
    //STATUS remains PENDING
}

$result = json_decode($response, true);
if(count($result) == 0){
    echo "Products unavailable";
    //STATUS REJECTED and exit
    if(!update_order_status($order_id, "REJECT")){
        echo "An error occured while updating order status";
    }
    exit("Order Rejected ".$order_id);
}else{
    echo "Products available\n";
    foreach($result as $product){
        $id = $key["_id"]["\$oid"];
        $quantity_avail = $product["QUANTITY"];
        $p_name = $product["NAME"];
        if($quantity_req[$id] > $quantity_avail){
            //STATUS REJECTED and exit
            if(!update_order_status($order_id, "REJECT")){
                echo "An error occured while updating order status";
            }
            exit("Order Rejected".$order_id);
        }else{
            echo "quantity avail: ".$quantity_avail."\n";
            echo "name: ".$p_name."\n";
            $quantity_remain[$id] = $quantity_avail - $quantity_req[$id];
        }
        // if every item of the order is available
        // update products db for all items in the order
        // even if one item has not enough quantity,
        // consider the order REJECTED
         
    }
    //here every product in the order is accepted 
    // request to API to 
    //1. update quantities in products db
    // 2. update order status
     
    
     foreach($product_id as $product){

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://php-apache-environment:80/api/products/update.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PATCH',
        CURLOPT_POSTFIELDS =>'{
            "pID":"'.$product.'",
            "quantity":'.$quantity_remain[$product].'
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if($httpcode != 200){
            echo "An error occured in updating product quantities\n";
            echo "product id: ".$product."\n";
            echo "quantity remain: ".$quantity_remain[$product]."\n";
            exit();
        }
    }

    //update orders db about an order status
    if(!update_order_status($order_id, "SUCCESS")){
        echo "An error occured while updating order status";
    }
    exit("Order Accepted ".$order_id);
}

function update_order_status($order_id, $new_status): bool{
    return true;

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://php-apache-environment:80/api/orders/update.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{
        "orderID":"'.$order_id.'",
        "status":"'.$new_status.'"
    }',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
    ),
    ));

    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if($httpcode != 200){
        echo("An error occured while updating order's status. ". curl_error($curl)." \nFor orderId: ".$order_id." \nHTTP response code: ".$httpcode);
        return false;
    }else{
        echo("Order status updated successfully");
        return true;
    }
}
?>