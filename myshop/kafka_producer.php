<?php
$conf = new RdKafka\Conf();
$conf->set('metadata.broker.list', 'kafka:19092'); 
// I am inside docker so i use the internal listener as suggested
//@ https://www.confluent.io/blog/kafka-listeners-explained/


$producer = new RdKafka\Producer($conf);

$topic = $producer->newTopic("test2");
echo "Topic name is: ".$topic->getName()."\n";

//for ($i = 0; $i < 10; $i++) {
    $var = rand(1, 20);
    echo $var."\n";
    $topic->produce(RD_KAFKA_PARTITION_UA, 0, "hi: ".$var);
    $producer->poll(0);
//}
//for ($flushRetries = 0; $flushRetries < 10; $flushRetries++) {
    $result = $producer->flush(30000);
    //  echo $result." flushretries ".$flushRetries;
    echo $result;
    echo "<br>";
    if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
        // break;
        echo "SUCCESS";

    }elseif (RD_KAFKA_RESP_ERR__TIMED_OUT === $result) {
        echo "TIMEOUT (-185)";
    }else{
        throw new \RuntimeException('Was unable to flush, messages might be lost!');
    }
//}

// if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
//     throw new \RuntimeException('Was unable to flush, messages might be lost!');
// }



?>