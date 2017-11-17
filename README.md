# Rabbit MQ PHP achieve

Introduction
---
This is a simple PHP achieve by php-amqplib.

Before you do below, RabbitMQ server should be started successfully.

Document is http://www.rabbitmq.com/tutorials/tutorial-one-php.html

Install
----

```
composer install
```

Worker (Consume)
---

```
#subscribe
php app/worker/cli.php --topic=User --subscribe


#queue
php app/worker/cli.php --topic=User
```


Pusher (Publish)
---

```
#subscribe
php app/pusher/cli.php --topic=User --router=a.b.c --message='Hello world!' --subscribe

#queue
php app/pusher/cli.php --topic=User --router=a.b.c --message='Hello world!'
```

Usage
---


`Usage: [--help][--topic=some][--queue=some][--router=some][--message=some][--subscribe]`


1. `--topic` Class file name. If used in SUBSCRIBE model ,it's exchange's topic.
2. `--queue` Queue name. only be used in QUEUE model
3. `--router` Router key. only be used in SUBSCRIBE model
4. `--message` Message. 
5. `--subscribe` use SUBSCRIBE model.

TODO
---

Tutorial steps:

1. [x] one
2. [x] two
3. [x] three
4. [x] four
5. [x] five
6. [ ] six
7. [ ] delay queue




LICENSE
---
MIT