# Rabbit MQ PHP achieve

Introduction
---
This is a simple PHP achieve by php-amqplib for Rabbit MQ.

* Support delay queue

Before you do below, RabbitMQ server should be started successfully.

Document is http://www.rabbitmq.com/tutorials/tutorial-one-php.html

Install
----

```
composer install
```

Worker (Consume)
---

### Use `Exchange`
```
php app/worker/cli.php --topic=User --exchange=user --queue=user
```

### Use `Queue`

```
php app/worker/cli.php --topic=User --queue=user
```

### Use `Exchange` to achieve `Delay Queue`

```
php app/worker/cli.php --topic=User --exchange=user --queue=user --delay=5

```
Pusher (Publish)
---

### Use `Exchange`
```
php app/pusher/cli.php --topic=User --queue=user --message='123' --router='a.b.c' --exchange=user
```

### Use `Queue`

```
php app/pusher/cli.php --topic=User --queue=user --message='123'
```

### Use `Exchange` to achieve `Delay Queue`

```
php app/pusher/cli.php --topic=User --queue=user --message='123' --router='a.b.c' --exchange=user  --delay=5
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


LICENSE
---
MIT