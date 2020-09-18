<?php


namespace G4\Messenger\Message;


class MessageFactory
{
    /**
     * @param array $data
     *
     * @return MessageEntity
     */
    public function create(array $data)
    {
        return new MessageEntity(
            $data['exchange_name'],
            $data['routing_key'],
            $data['message'],
            $data['ts_created']
        );
    }

    /**
     * @param array $data
     *
     * @return MessageEntity
     */
    public function reconstitute(array $data)
    {
        return new MessageEntity(
            $data['exchange_name'],
            $data['routing_key'],
            json_decode($data['message'], true),
            $data['ts_created'],
            $data['id']
        );
    }
}