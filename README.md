# Messenger

> messenger library 

## Install
Via Composer

```sh
composer require g4/messenger
```

## Usage

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