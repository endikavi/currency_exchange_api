<?php

//check if a function named "RabbiMQCall" exists
if (!function_exists('RabbiMQCall')) {
    //if not, create it
    function RabbiMQCall($to, $from, $value)
    {
        if(env('RABBITMQ_HOST') == 'localhost'){
            $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
            $channel = $connection->channel();
            $channel->queue_declare('quote', false, false, false, false);
            $msg = new AMQPMessage($to . ',' . $from . ',' . $value);
            $channel->basic_publish($msg, '', 'quote');
        }
    }
}