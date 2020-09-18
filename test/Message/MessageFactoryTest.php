<?php


class MessageFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $time = time();

        $data = [
            'exchange_name' => 'exchange_name_test',
            'routing_key' => 'routing_key_test',
            'message_body' => ['foo' => 'bar'],
            'ts_created' => $time,
        ];

        $entity = (new \G4\Messenger\Message\MessageFactory())->create($data);

        $this->assertInstanceOf(\G4\Messenger\Message\MessageEntity::class, $entity);
        $this->assertNull($entity->getId());
        $this->assertEquals('exchange_name_test', $entity->getExchangeName());
        $this->assertEquals('routing_key_test', $entity->getRoutingKey());
        $this->assertEquals(['foo' => 'bar'], $entity->getMessageBody());
        $this->assertEquals($time, $entity->getTsCreated());
    }

    public function testReconstitute()
    {
        $time = time();

        $data = [
            'exchange_name' => 'exchange_name_test',
            'routing_key' => 'routing_key_test',
            'message_body' => json_encode(['foo' => 'bar']),
            'ts_created' => $time,
            'id' => 123
        ];

        $entity = (new \G4\Messenger\Message\MessageFactory())->reconstitute($data);

        $this->assertInstanceOf(\G4\Messenger\Message\MessageEntity::class, $entity);
        $this->assertEquals(123, $entity->getId());
        $this->assertEquals('exchange_name_test', $entity->getExchangeName());
        $this->assertEquals('routing_key_test', $entity->getRoutingKey());
        $this->assertEquals(['foo' => 'bar'], $entity->getMessageBody());
        $this->assertEquals($time, $entity->getTsCreated());
    }
}
