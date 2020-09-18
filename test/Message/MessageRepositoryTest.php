<?php


class MessageRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var \PDOStatement
     */
    private $smtp;

    /**
     * @var \G4\Messenger\Message\MessageEntity
     */
    private $entity;

    /**
     * @var \G4\Messenger\Message\MessageRepository
     */
    private $repository;

    public function setUp()
    {
        $this->pdo = $this->createMock(\PDO::class);
        $this->smtp = $this->createMock(\PDOStatement::class);
        $this->entity = $this->createMock(\G4\Messenger\Message\MessageEntity::class);

        $this->repository = new G4\Messenger\Message\MessageRepository($this->pdo);
    }

    public function testAdd()
    {
        $query = 'INSERT INTO rbmq_messages (exchange_name, routing_key, message_body, ts_created) VALUES (:exchange_name, :routing_key, :message_body, :ts_created)';

        $this->pdo
            ->expects($this->once())
            ->method('prepare')
            ->with($query)
            ->willReturn($this->smtp);

        $this->entity
            ->expects($this->once())
            ->method('getExchangeName')
            ->willReturn('exchange_name');

        $this->entity
            ->expects($this->once())
            ->method('getRoutingKey')
            ->willReturn('routing_key_name');

        $this->entity
            ->expects($this->once())
            ->method('getMessageBody')
            ->willReturn(['foo' => 'bar']);

        $this->entity
            ->expects($this->once())
            ->method('getTsCreated')
            ->willReturn(time());

        $this->smtp
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->entity
            ->expects($this->once())
            ->method('setId');

        $result = $this->repository->add($this->entity);
        $this->assertInstanceOf(\G4\Messenger\Message\MessageEntity::class, $result);
    }

    public function testFindAll()
    {
        $query = sprintf('SELECT * FROM rbmq_messages order by ts_created ASC limit %s', 20);

        $this->pdo
            ->expects($this->once())
            ->method('query')
            ->with($query)
            ->willReturn($this->smtp);

        $this->smtp
            ->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_ASSOC)
            ->willReturn([[
                'exchange_name' => 'exchange_name_test',
                'routing_key' => 'routing_key_test',
                'message_body' => json_encode(['foo' => 'bar']),
                'ts_created' => time(),
                'id' => 123
            ]]);

        $results = $this->repository->findAll();
        $this->assertContainsOnlyInstancesOf(\G4\Messenger\Message\MessageEntity::class, $results);
    }

    public function testDelete()
    {
        $query = 'DELETE FROM rbmq_messages WHERE id = :id';

        $this->entity
            ->expects($this->once())
            ->method('getId')
            ->willReturn(123);

        $this->pdo
            ->expects($this->once())
            ->method('prepare')
            ->with($query)
            ->willReturn($this->smtp);

        $this->smtp
            ->expects($this->once())
            ->method('bindValue')
            ->with(':id', 123);

        $this->smtp
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->repository->delete($this->entity);
    }
}