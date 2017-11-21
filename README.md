# Rabbit MQ PHP achieve

Introduction
---
This is a simple PHP achieve by php-amqplib for Rabbit MQ.

* Support delay queue

Before you do below, RabbitMQ server should be started successfully.

Document is http://www.rabbitmq.com/tutorials/tutorial-one-php.html

about name `RMQP` means " Rabbit Message Queue Php "

Install
----

```
composer install
```

Worker (Consume)
---

### Use `Exchange`
```
php RMQP/worker/cli.php --topic=Test --exchange=test --queue=test
```

### Use `Queue`

```
php RMQP/worker/cli.php --topic=Test --queue=test
```

### Use `Exchange` to achieve `Delay Queue`

```
php RMQP/worker/cli.php --topic=Test --exchange=test --queue=test --delay=5

```
Pusher (Publish)
---

### Use `Exchange`
```
php RMQP/pusher/cli.php --topic=Test --queue=test --message='Hello' --router='a.b.c' --exchange=test
```

### Use `Queue`

```
php RMQP/pusher/cli.php --topic=Test --queue=test --message='Hello'
```

### Use `Exchange` to achieve `Delay Queue`

```
php RMQP/pusher/cli.php --topic=Test --queue=test --message='Hello' --router='a.b.c' --exchange=test  --delay=5
```

Usage
---


`Usage: [--help][--topic=string][--exchange=string][--queue=string][--router=string][--message=string][--delay=number]`


0. `--topic` Class file name.
0. `--exchange` Exchange name.
0. `--queue` Queue name. 
0. `--router` Router key. 
0. `--message` Message body. 
0. `--delay` Delay second(s).

TODO
---

Tutorial steps:

0. [x] one
0. [x] two
0. [x] three
0. [x] four
0. [x] five
0. [x] six

Delay queue:

0. [x] delay queue

ATTENTION
---
Delay queue achieved by `TTL` and `DLX`, different `delay` number(seconds) in only one queue will cause queue blocked. We must make a deal that procedure should create separate queues and exchanges for each number(seconds) of seconds. 

Exchange do not support different `x-dead-letter-router-key` so that procedure will create separate  exchanges for each router keys. 

The above needs to be considered in the operation and maintenance (OP) layer.

LICENSE
---
MIT