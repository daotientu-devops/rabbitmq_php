<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
// P
$channel = $connection->channel();
// X
$channel->exchange_declare('logs', 'fanout', false, false, false); // exchange_type=fanout
// Q
list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);
$channel->queue_bind($queue_name, 'logs');
echo " [*] Waiting for logs. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] ', $msg->body, "\n";
};
// C
$channel->basic_consume($queue_name, '', false, true, false, false, $callback);
while($channel->is_open()) {
    $channel->wait();
}
$channel->close();
$connection->close();