<?php


namespace G4\Messenger\Message;

use G4\Messenger\Message\Consts\MessageConsts;

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
            $data[MessageConsts::EXCHANGE_NAME],
            $data[MessageConsts::ROUTING_KEY],
            $data[MessageConsts::MESSAGE_BODY],
            $data[MessageConsts::TS_CREATED]
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
            $data[MessageConsts::EXCHANGE_NAME],
            $data[MessageConsts::ROUTING_KEY],
            json_decode($data[MessageConsts::MESSAGE_BODY], true),
            $data[MessageConsts::TS_CREATED],
            $data[MessageConsts::MESSAGE_ID]
        );
    }
}