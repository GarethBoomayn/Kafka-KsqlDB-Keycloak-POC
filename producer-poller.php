<?php
// producer-poller.php

if (!extension_loaded('rdkafka')) {
    echo "php-rdkafka extension not loaded!\n";
    exit(1);
}

$conf = new RdKafka\Conf();
$conf->set('bootstrap.servers', 'kafka-1:19092,kafka-2:19093,kafka-3:19094');

$producer = new RdKafka\Producer($conf);
$topic = $producer->newTopic("notification"); // like in the PDF

while (true) {
    // Generate random data
    $randBytes = random_bytes(5);
    $message = base64_encode($randBytes);
    
    $payload = json_encode([
        'id' => null,
        'infoToTrack' => $message,
        'sysDate' => date('c'),
    ]);
    
    // Produce the message
    $topic->produce(RD_KAFKA_PARTITION_UA, 0, $payload);
    $producer->poll(0);
    // Flush to ensure delivery
    for ($flushRetries = 0; $flushRetries < 10; $flushRetries++) {
        $result = $producer->flush(1000);
        if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
            break;
        }
    }
    
    echo "Produced: $payload\n";
    
    // Sleep for 5s (or however often you want)
    sleep(5);
}
