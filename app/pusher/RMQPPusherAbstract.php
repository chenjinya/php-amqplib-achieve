<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午8:08
 */

namespace app\pusher;

use app\worker\exception\ParamErrorException;
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
    protected $topic = '';
    protected $queue_name = '';
    protected $router_key = '';
    protected $option = [];
    protected $is_subscribe = false;

    /**
     * RMQPPusherAbstract constructor.
     * @param $topic
     * @param $option
     * @param $queue_name
     * @throws ParamErrorException
     */
    public function __construct($topic = null, $queue_name = null, $option = []){

        $this->topic = $topic;
        if(empty($this->topic)) {
            throw new ParamErrorException('Param `topic` is require');
        }
        $this->queue_name = $queue_name;
        $this->option = $option;

        if(isset($this->option['subscribe']) && $this->option['subscribe']) {
            $this->is_subscribe = true;
            Console::info("pusher model is [SUBSCRIBE]", [], __METHOD__);
        } else {
            if(empty($this->queue_name)) {
                throw new ParamErrorException('model [QUEUE] param `queue_name` is require');
            }
            Console::info("Pusher model is [QUEUE]", [], __METHOD__);
        }
        $this->connection = new AMQPStreamConnection(self::HOST, self::PORT, self::USER, self::PASS);
        $this->channel = $this->connection->channel();
        $this->prepare();

    }

    /**
     * set some declare not only include exchange or queue
     */
    public function prepare(){
        if($this->is_subscribe) {
            $this->channel->exchange_declare($this->topic, 'topic', false, false, false);
        } else {
            $this->channel->queue_declare(
                $this->queue_name,
                false,
                $durable= true, /* persistent queue */
                false,
                false
            );
        }

    }

    /**
     * push task
     * @param $router_key
     * @param $payload
     */
    public function push($payload, $router_key){

        $msg = new AMQPMessage(
            $payload,
            [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,// persistent message
            ]);
        if($this->is_subscribe) {
             $this->channel->basic_publish($msg, $this->topic, $router_key);
        } else {
             $this->channel->basic_publish($msg, '', $this->queue_name);
        }

    }

    /**
     * push delay task
     * @param $router_key
     * @param $payload
     * @param $delay
     */
    public function delayPush($payload, $delay, $router_key)
    {
        // TODO: Implement delayPush() method.
    }

    /**
     * destruct should close all connection
     */
    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}