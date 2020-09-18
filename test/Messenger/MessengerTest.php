<?php

use G4\Messenger\Messenger\Messenger;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class MessengerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var \PDO
     */
    private $pdo;

    public function testInstantiate()
    {
        $this->connection = $this->createMock(AMQPStreamConnection::class);
        $this->pdo = $this->createMock(\PDO::class);

        $this->assertInstanceOf(Messenger::class, new Messenger($this->pdo,$this->connection));
    }
}