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
 * 3. cron job consumes msg, call to Products API, update quantity, push to kafka  products_topic
 * 4. cron job consumes msg, call to Orders API, update status
 */



$quantity_remain = array();
$quantity_req =  1;
$product_id =array();
$product_id[] = '65567a231155ad8c9675f2ed'; 
$product_id[] = '65567a931155ad8c9675f2ef'; 


// $product_id = '65567a231155ad8c9675f2ed';
//call to Products API to read a product info

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
        $quantity_avail = $product["QUANTITY"];
        $p_name = $product["NAME"];
        if($quantity_req > $quantity_avail){
            //STATUS REJECTED and exit
            if(!update_order_status($order_id, "REJECT")){
                echo "An error occured while updating order status";
            }
            exit("Order Rejected".$order_id);
        }else{
            echo "quantity avail: ".$quantity_avail."\n";
            echo "name: ".$p_name."\n";
            $quantity_remain[] = $quantity_avail - $quantity_req;
        }
        /**if every item of the order is available
         * update products db for all items in the order
         * even if one item has not enough quantity,
         * consider the order REJECTED
         */
    }
    //here every product in the order is accepted
    //request to API to 1.update db and 2. push msg to kafka

    for($i = 0; $i < count($product_id); $i++){

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
            "pID":"'.$product_id[$i].'",
            "quantity":'.$quantity_remain[$i].'
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
            echo "product id: ".$product_id[$i]."\n";
            echo "quantity remain: ".$quantity_remain[$i]."\n";
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
}
?>