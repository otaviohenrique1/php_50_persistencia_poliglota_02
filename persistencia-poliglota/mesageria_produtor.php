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

$msg = new AMQPMessage
(body:'1234');
$channel->basic_publish($msg, exchange:'', routing_key:'product_bought');

echo "Mensagem enviada \n";

$channel->close();
$connection->close();