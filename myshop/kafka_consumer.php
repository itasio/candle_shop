<?php

$conf = new RdKafka\Conf();

// Configure the group.id. All consumer with the same group.id will consume
// different partitions.
$conf->set('group.id', 'myConsumerGroup');

// Initial list of Kafka brokers
// I am inside docker so i use the internal listener as suggested
//@ https://www.confluent.io/blog/kafka-listeners-explained/
$conf->set('metadata.broker.list', 'kafka:19092');

// Set where to start consuming messages when there is no initial offset in
// offset store or the desired offset is out of range.
// 'earliest': start from the beginning
$conf->set('auto.offset.reset', 'earliest');

// Emit EOF event when reaching the end of a partition
$conf->set('enable.partition.eof', 'true');

$consumer = new RdKafka\KafkaConsumer($conf);

// Subscribe to topic 'test'
$consumer->subscribe(['testtt']);

echo "Waiting for partition assignment... (make take some time when\n";
echo "quickly re-joining the group after leaving it.)\n";

while (true) {
    $message = $consumer->consume(120*1000);
    switch ($message->err) {
        case RD_KAFKA_RESP_ERR_NO_ERROR:
            //var_dump($message);
            // echo "Payload of msg is: ";
            // var_dump($message->payload);
            $order = json_decode($message->payload);
            $order_id = $order->id;
            echo $order_id."\n";
            // $products = $order->products;
            foreach($order->products as $product){
                $name = $product->name;
                $amount = $product->amount;
                $product_id = $product->PRODUCT_ID;
                echo $name." ".$amount." ".$product_id." \n";
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
}
?>