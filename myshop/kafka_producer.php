<?php
$conf = new RdKafka\Conf();
$conf->set('metadata.broker.list', 'kafka:8097');


$producer = new RdKafka\Producer($conf);

$topic = $producer->newTopic("test2");
echo $topic->getName();
echo "hi1";
for ($i = 0; $i < 10; $i++) {
    $topic->produce(RD_KAFKA_PARTITION_UA, 0, "Message $i");
    $producer->poll(0);
}
echo "hi2";
for ($flushRetries = 0; $flushRetries < 10; $flushRetries++) {
    $result = $producer->flush(10000);
    if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
        break;
    }
}
echo "hi3";

if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
    throw new \RuntimeException('Was unable to flush, messages might be lost!');
}



?>