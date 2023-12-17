<?php
$conf = new RdKafka\Conf();
$conf->set('metadata.broker.list', 'kafka:19092'); 
// I am inside docker so i use the internal listener as suggested
//@ https://www.confluent.io/blog/kafka-listeners-explained/
?>