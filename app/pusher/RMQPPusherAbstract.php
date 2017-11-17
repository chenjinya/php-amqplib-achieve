<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午8:08
 */

namespace app\pusher;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use app\lib\Console;


class RMQPPusherAbstract implements RMQPPusherInterface
{

    const HOST = 'localhost';
    const PORT = 5672;
    const USER = 'guest';
    const PASS = 'guest';
    protected  $connection = null;
    protected  $channel = null;
    protected $queue_name = '';

    public function __construct($queue_name){
        $this->connection = new AMQPStreamConnection(self::HOST, self::PORT, self::USER, self::PASS);
        $this->channel = $this->connection->channel();
        $this->queue_name = $queue_name;
        $this->prepare();

    }

    public function prepare(){
        $this->channel->queue_declare(
            $this->queue_name,
            false,
            $durable= true, /* persistent queue */
            false,
            false
        );
    }

    public function push($payload){

        $msg = new AMQPMessage(
            $payload,
            [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,// persistent message
            ]);
        $this->channel->basic_publish($msg, '', $this->queue_name);

    }
    public function delayPush($payload, $delay)
    {
        // TODO: Implement delayPush() method.
    }
}