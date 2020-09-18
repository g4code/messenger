<?php

namespace G4\Messenger\RabbitMQ;

use G4\Messenger\Message\MessageFactory;
use G4\Messenger\Message\MessageRepository;
use G4\Messenger\RabbitMQ\Consts\RabbitMqConsts;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class RabbitMq
 * @package G4\Messenger\RabbitMQ
 */
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

    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct(
        $exchangeName,
        $routingKey,
        \PDO $pdo,
        AMQPStreamConnection $connection = null
    ) {
        $this->exchangeName = $exchangeName;
        $this->routingKey = $routingKey;
        $this->pdo = $pdo;
        $this->connection = $connection;
    }

    /**
     * @param array $messageData
     */
    public function sendMessage(array $messageData)
    {
        if (!$this->connection instanceof AMQPStreamConnection) {
            $this->createMessage($messageData);
            return;
        }

        $channel = $this->connection->channel();

        try {
            $msg = new AMQPMessage(json_encode($messageData), [RabbitMqConsts::DELIVERY_MODE => RabbitMqConsts::PERSISTENT_DELIVERY_MODE]);
            $channel->basic_publish($msg, $this->exchangeName, $this->routingKey);
        } catch (\Exception $e) {
            $this->createMessage($messageData);
        } finally {
            $channel->close();
        }
    }

    public function sendMessageBulk(array $messageData)
    {
        $messages = $this->getAMQPMessages($messageData);

        if (!$this->connection instanceof AMQPStreamConnection) {
            $this->createMessage($messageData);

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
                $this->createMessage($messageData);
            }
        }

        $channel->publish_batch();
        $channel->close();
    }

    /**
     * @param array $data
     * @return array
     */
    private function getAMQPMessages(array $data)
    {
        return array_map(function ($messageData) {
            return new AMQPMessage(json_encode($messageData), [RabbitMqConsts::DELIVERY_MODE => RabbitMqConsts::PERSISTENT_DELIVERY_MODE]);
        }, $data);
    }

    /**
     * Store failed rmq message to db.
     *
     * @param array $messageData
     */
    private function createMessage(array $messageData)
    {
        $message = (new MessageFactory())->create(
            [
                'exchange_name' => $this->exchangeName,
                'routing_key' => $this->routingKey,
                'message' => $messageData,
                'ts_created' => time()
            ]
        );

        (new MessageRepository($this->pdo))->add($message);
    }
}