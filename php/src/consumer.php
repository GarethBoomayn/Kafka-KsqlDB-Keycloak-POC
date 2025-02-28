<?php
// consumer.php

if (!extension_loaded('rdkafka')) {
    echo "php-rdkafka extension not loaded!\n";
    exit(1);
}

// Create configuration for the consumer
$conf = new RdKafka\Conf();

// Set the group ID (must be unique per consumer group)
$conf->set('group.id', 'my-php-consumer-group');

// Bootstrap servers (same ones used by your producer)
$conf->set('metadata.broker.list', 'kafka-1:19092,kafka-2:19093,kafka-3:19094');

// Offset reset (if no offset is stored for this group, where to start?)
$conf->set('auto.offset.reset', 'earliest');

// Create consumer
$consumer = new RdKafka\KafkaConsumer($conf);

// Subscribe to one or more topics
$consumer->subscribe(['jeuxvideo']); // or whatever topic name you use

echo "Starting the consumer... listening on topic 'jeuxvideo'\n";

while (true) {
    try {
        $message = $consumer->consume(120*1000); // 120 seconds
        switch ($message->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                // Successful read
                echo "Message received:\n";
                echo $message->payload . "\n";
                // Here you can decode JSON or do further processing
                break;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                // No more messages, keep polling
                echo "No more messages; will continue listening...\n";
                break;
            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                // Poll timeout, keep trying
                echo "Timed out; no messages in this interval\n";
                break;
            default:
                // Some error
                echo "Error occurred: " . $message->errstr() . "\n";
                break;
        }
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage() . "\n";
    }
}
