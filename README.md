# Rabbit MQ PHP achieve

0 Introduction
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
php app/worker/cli.php User
```

`User` is queue name and class name

Pusher (Publish)
---

```
php app/pusher/cli.php User 'Hello'
```

`User` is queue name and class name

`'Hello'` is send message


TODO
---

[x] one
[x] two
[] three
[] four
[] five
[] six




LICENSE
---
MIT