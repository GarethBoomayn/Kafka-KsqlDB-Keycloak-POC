<?php
// producer.php

/**
 * Produce a message to a Kafka topic.
 *
 * @param string $topicName
 * @param string $info
 * @return bool  True if successful, false otherwise
 */
function produceMessage($topicName, $info)
{
    if (!extension_loaded('rdkafka')) {
        error_log("php-rdkafka extension not loaded!");
        return false;
    }

    // Configuration
    $conf = new RdKafka\Conf();
    // Use the internal Docker hostnames for Kafka:
    $conf->set('bootstrap.servers', 'kafka-1:19092,kafka-2:19093,kafka-3:19094');

    $producer = new RdKafka\Producer($conf);
    if (!$producer) {
        error_log("Failed to create producer");
        return false;
    }

    // Create or get the topic object
    $topic = $producer->newTopic($topicName);

    // Build the message payload
    $messageToSend = json_encode([
        "id" => null,
        "infoToTrack" => $info,
        "sysDate" => date('c'),
    ]);

    // Produce the message
    $topic->produce(RD_KAFKA_PARTITION_UA, 0, $messageToSend);

    // Poll / flush
    $producer->poll(0);
    for ($flushRetries = 0; $flushRetries < 10; $flushRetries++) {
        $result = $producer->flush(1000);
        if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
            return true;
        }
    }

    error_log("Unable to flush; message might be lost.");
    return false;
}
