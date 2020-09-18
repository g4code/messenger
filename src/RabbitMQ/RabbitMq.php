<?php

namespace G4\Messenger\RabbitMQ;

use G4\Messenger\RabbitMQ\Consts\RabbitMqConsts;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMq
{
    /**
     * @var string
     */
    private $exchangeName;

    /**
     * @var string
     */
    private $routingKey;

    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    public function __construct(
        $exchangeName,
        $routingKey,
        AMQPStreamConnection $connection = null
    ) {
        $this->exchangeName = $exchangeName;
        $this->routingKey = $routingKey;
        $this->connection = $connection;
    }

    /**
     * @param array $messageData
     */
    public function sendMessage(array $messageData)
    {
        if (!$this->connection instanceof AMQPStreamConnection) {
            //@todo write to messages db
            return;
        }

        $channel = $this->connection->channel();

        try {
            $msg = new AMQPMessage(json_encode($messageData), [RabbitMqConsts::DELIVERY_MODE => RabbitMqConsts::PERSISTENT_DELIVERY_MODE]);
            $channel->basic_publish($msg, $this->exchangeName, $this->routingKey);
        } catch (\Exception $e) {
            // @todo write to messages db
        } finally {
            $channel->close();
        }
    }

    public function sendMessageBulk(array $messageData)
    {
        $messages = $this->getMessages($messageData);

        if (!$this->connection instanceof AMQPStreamConnection) {
            //@todo write all messages to db

            return;
        }

        $channel = $this->connection->channel();

        foreach ($messages as $message) {
            try {
                $channel->batch_basic_publish(
                    $message,
                    $this->exchangeName,
                    $this->routingKey
                );
            } catch (\Exception $exception) {
                //@todo write to db
            }
        }

        $channel->publish_batch();
        $channel->close();
    }

    /**
     * @param array $data
     * @return array
     */
    private function getMessages(array $data)
    {
        return array_map(function ($messageData) {
            return new AMQPMessage(json_encode($messageData), [RabbitMqConsts::DELIVERY_MODE => RabbitMqConsts::PERSISTENT_DELIVERY_MODE]);
        }, $data);
    }
}