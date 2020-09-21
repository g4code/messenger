# Messenger

> messenger library 

## Install
Via Composer

```sh
composer require g4/messenger
```

## Usage

To create rbmq messages table run:
```sh
php vendor/bin/messenger-create-table --dbname your_db_name --dbport your_db_port --username your_db_username --password your_db_password --host your_db_host
```

```php
<?php 

// create \PDO object with your db params
$pdo = new \PDO('mysql:dbname=db;host=127.0.0.1:3306', 'root', '1234');

// publish singe message to RabbitMq with your RabbitMq params
(new \G4\Messenger\RabbitMQ\RabbitMq(
    'your_exchange',
    'your_binding',
    $pdo
))->sendMessage(['foo' => 'baz']);

// publish bulk messages to RabbitMq with your RabbitMq params
(new \G4\Messenger\RabbitMQ\RabbitMq(
    'your_exchange',
    'your_binding',
    $pdo
))->sendMessageBulk(
    [
        ['foo1' => 'baz1'],
        ['foo2' => 'baz2'],
        ['foo3' => 'baz3'],
    ]
);


// create your AMQP connection based on your params
$connection =  new PhpAmqpLib\Connection\AMQPStreamConnection(
      'localhost',
       '5672',
       'guest',
       'guest'
);

// restore your undelivered message from db and put them back to RBMQ
(new \G4\Messenger\Messenger\Messenger(
    $pdo,
    $connection
))->restoreMessages();

?>
```

## Development

### Install dependencies

    $ make install

### Run tests

    $ make unit-tests