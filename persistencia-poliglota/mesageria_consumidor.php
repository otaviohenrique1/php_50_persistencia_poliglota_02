<?php

require_once 'vendor/autoload.php';

use PhpAmqplib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection(
  host: 'localhost',
  port: 5672, 
  user: 'guest', 
  password: 'guest'
);

$channel = $connection->channel();
$channel->queue_declare('product_bought', auto_delete: false);

$channel->basic_consume('product_bought', no_ack: true, callback: function(AMQPMessage $message) {
  echo "[x] Mensagem recebida: " .$message->getBody() . PHP_EOL;
});

try {
  $channel->consume();
} catch (\Throwable $exception) {
  var_dump($exception);
}

$channel->close();
$connection->close();