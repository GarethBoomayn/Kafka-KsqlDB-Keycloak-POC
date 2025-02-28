<?php
// index.php

require_once __DIR__ . '/producer.php';

// Parse the input
$topicName = $_GET['topic'] ?? 'testtopic';
$info = $_GET['info'] ?? 'DefaultInfo';

// Produce the message
$result = produceMessage($topicName, $info);

if ($result) {
    echo "Message successfully produced to topic '$topicName'.\n";
    echo "Content: $info\n";
} else {
    echo "Failed to produce message.\n";
}
