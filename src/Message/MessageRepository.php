<?php


namespace G4\Messenger\Message;


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
        $query = 'INSERT INTO rmq_messages (exchange_name, routing_key, message, ts_created) VALUES (:exchange_name, :routing_key, :message, :ts_created)';

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
        $query = sprintf('SELECT * FROM rmq_messages order by ts_created ASC limit %s', $limit);
        $stmt = $this->pdo->query($query);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function($data) {
            return (new MessageFactory())->reconstitute($data);
        }, $results);
    }

    private function prepareStatement(\PDOStatement $stmt, MessageEntity $entity)
    {
        $stmt->bindValue(':exchange_name', $entity->getExchangeName(), \PDO::PARAM_STR);
        $stmt->bindValue(':routing_key', $entity->getRoutingKey(), \PDO::PARAM_STR);
        $stmt->bindValue(':message', json_encode($entity->getMessage()), \PDO::PARAM_STR);
        $stmt->bindValue(':ts_created', $entity->getTsCreated(), \PDO::PARAM_INT);
    }
}