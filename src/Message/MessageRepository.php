<?php


namespace G4\Messenger\Message;


use Model\Domain\EmailPaid\EmailPaidEntity;

class MessageRepository
{
    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param MessageEntity $entity
     * @return MessageEntity
     */
    public function add(MessageEntity $entity)
    {
        $query = 'INSERT INTO rbmq_messages (exchange_name, routing_key, message_body, ts_created) VALUES (:exchange_name, :routing_key, :message_body, :ts_created)';

        $stmt = $this->pdo->prepare($query);
        $this->prepareStatement($stmt, $entity);

        if (!$stmt->execute()) {
            throw new \RuntimeException(json_encode($stmt->errorInfo()));
        }

        $entity->setId($this->pdo->lastInsertId());

        return $entity;
    }

    /**
     * @param int $limit
     * @return array | MessageEntity[]
     */
    public function findAll($limit = 20)
    {
        $query = sprintf('SELECT * FROM rbmq_messages order by ts_created ASC limit %s', $limit);
        $stmt = $this->pdo->query($query);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function($data) {
            return (new MessageFactory())->reconstitute($data);
        }, $results);
    }

    /**
     * @param MessageEntity $entity
     */
    public function delete(MessageEntity $entity)
    {
        $query = 'DELETE FROM rbmq_messages WHERE id = :id';

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', $entity->getId());

        if (!$stmt->execute()) {
            throw new \RuntimeException(json_encode($stmt->errorInfo()));
        }
    }

    private function prepareStatement(\PDOStatement $stmt, MessageEntity $entity)
    {
        $stmt->bindValue(':exchange_name', $entity->getExchangeName(), \PDO::PARAM_STR);
        $stmt->bindValue(':routing_key', $entity->getRoutingKey(), \PDO::PARAM_STR);
        $stmt->bindValue(':message_body', json_encode($entity->getMessageBody()), \PDO::PARAM_STR);
        $stmt->bindValue(':ts_created', $entity->getTsCreated(), \PDO::PARAM_INT);
    }
}