<?php
// transformer.php

if (!extension_loaded('rdkafka')) {
    echo "php-rdkafka extension not loaded!\n";
    exit(1);
}

// ---- Consumer setup ----
$consumerConf = new RdKafka\Conf();
// You need a group.id:
$consumerConf->set('group.id', 'php-transformer-group');
// If no offset is known, start at earliest:
$consumerConf->set('auto.offset.reset', 'earliest');
// Connect to your Kafka brokers (on the Docker network):
$consumerConf->set('bootstrap.servers', 'kafka-1:19092,kafka-2:19093,kafka-3:19094');

$consumer = new RdKafka\KafkaConsumer($consumerConf);
// Subscribe to the "in" topic:
$consumer->subscribe(['notification']);

// ---- Producer setup ----
$producerConf = new RdKafka\Conf();
$producerConf->set('bootstrap.servers', 'kafka-1:19092,kafka-2:19093,kafka-3:19094');
$producer = new RdKafka\Producer($producerConf);

// The "out" topic:
$outTopic = $producer->newTopic('blog');

echo "Starting transformer: reading from 'notification', writing to 'blog'\n";

// ---- Main loop ----
while (true) {
    // Wait up to 2 minutes (120*1000 ms) for a message
    $msg = $consumer->consume(120 * 1000);

    switch ($msg->err) {
        case RD_KAFKA_RESP_ERR_NO_ERROR:
            // We got a message successfully
            $payload = $msg->payload;
            echo "Received msg: $payload\n";

            // 1) Transform: parse JSON, set id=99
            $data = json_decode($payload, true);
            if (is_array($data)) {
                $data['id'] = 99;
            } else {
                // Not JSON? Just make it a simple format
                $data = ['id' => 99, 'rawMessage' => $payload];
            }

            // 2) Produce to "blog"
            $newPayload = json_encode($data);
            $outTopic->produce(RD_KAFKA_PARTITION_UA, 0, $newPayload);
            $producer->poll(0);

            // Flush to ensure it's sent
            for ($flushRetries = 0; $flushRetries < 10; $flushRetries++) {
                $result = $producer->flush(1000);
                if ($result === RD_KAFKA_RESP_ERR_NO_ERROR) {
                    break;
                }
            }

            echo "Transformed & published: $newPayload\n";
            break;

        case RD_KAFKA_RESP_ERR__PARTITION_EOF:
            // Partition EOF, keep looping
            echo "End of partition event\n";
            break;

        case RD_KAFKA_RESP_ERR__TIMED_OUT:
            echo "Timed out (no new messages)\n";
            break;

        default:
            // Some error
            echo "Error: {$msg->errstr()}\n";
            break;
    }
}
