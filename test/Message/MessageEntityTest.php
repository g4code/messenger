<?php


class MessageEntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \G4\Messenger\Message\MessageEntity
     */
    private $entity;

    private $exchangeName;

    private $routingKey;

    private $message;

    private $tsCreated;

    private $id;

    public function setUp()
    {
        $this->exchangeName = 'exchange_name';
        $this->routingKey = 'routing_key';
        $this->message = ['foo' => 'bar'];
        $this->tsCreated = time();
        $this->id = 123;

        $this->entity = new G4\Messenger\Message\MessageEntity(
            $this->exchangeName,
            $this->routingKey,
            $this->message,
            $this->tsCreated,
            $this->id
        );
    }

    public function testGetters()
    {
        $this->assertEquals($this->exchangeName, $this->entity->getExchangeName());
        $this->assertEquals($this->routingKey, $this->entity->getRoutingKey());
        $this->assertEquals($this->message, $this->entity->getMessage());
        $this->assertEquals($this->tsCreated, $this->entity->getTsCreated());
        $this->assertEquals($this->id, $this->entity->getId());
    }
}