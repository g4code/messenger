<?php


namespace G4\Messenger\Message;

class MessageEntity
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
     * @var array
     */
    private $message;

    /**
     * @var integer
     */
    private $tsCreated;

    /**
     * @var integer
     */
    private $id;

    /**
     * MessageEntity constructor.
     * @param string $exchangeName
     * @param string $routingKey
     * @param array $message
     * @param integer $tsCreated
     * @param integer $id
     */
    public function __construct(
        $exchangeName,
        $routingKey,
        array $message,
        $tsCreated,
        $id = null
    ) {
        $this->exchangeName = $exchangeName;
        $this->routingKey = $routingKey;
        $this->message = $message;
        $this->tsCreated = $tsCreated;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getExchangeName()
    {
        return $this->exchangeName;
    }

    /**
     * @return string
     */
    public function getRoutingKey()
    {
        return $this->routingKey;
    }

    /**
     * @return array
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getTsCreated()
    {
        return $this->tsCreated;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }
}