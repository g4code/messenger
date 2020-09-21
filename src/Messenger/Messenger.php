<?php

namespace G4\Messenger\Messenger;

use G4\Messenger\Message\MessageRepository;
use G4\Messenger\RabbitMQ\RabbitMq;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Messenger
{
    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var \PDO
     *
     */
    private $pdo;

    /**
     * Messenger constructor.
     * @param \PDO $pdo
     * @param AMQPStreamConnection $connection
     */
    public function __construct(
        \PDO $pdo,
        AMQPStreamConnection $connection
    ) {
        $this->pdo = $pdo;
        $this->connection = $connection;
    }

    public function restoreMessages($limit = 250)
    {
        $repository = new MessageRepository($this->pdo);

        $messages = $repository->findAll($limit);
        foreach ($messages as $message) {
            (new RabbitMq(
                $message->getExchangeName(),
                $message->getRoutingKey(),
                $this->pdo,
                $this->connection
            ))
                ->sendMessage($message->getMessageBody());

            $repository->delete($message);
        }
    }
}