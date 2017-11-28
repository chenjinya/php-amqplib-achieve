<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午8:08
 */

namespace RMQP\lib;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;


abstract class RMQPPusherAbstract implements RMQPPusherInterface
{

    const HOST = Config::HOST;
    const PORT = Config::PORT;
    const USER = Config::USER;
    const PASS = Config::PASS;

    protected  $connection = null;
    protected  $channel = null;

    protected $exchange = '';
    protected $queue_name = '';
    protected $delay = 0;


    /**
     * RMQPPusherAbstract constructor.
     * @param $exchange
     * @param $queue_name
     * @param $delay
     * @throws ParamErrorException
     */
    public function __construct($exchange = '', $queue_name = '', $delay = 0){

        $this->exchange   = $exchange;
        $this->queue_name = $queue_name;
        $this->delay      = $delay;
        if(empty($this->exchange) && empty($this->queue_name)) {
            throw new ParamErrorException('Param `exchange` or `queue_name` is require');
        }


        if($this->exchange) {
            Console::debug("Pusher model is [EXCHANGE]", [
                'exchange'      => $exchange,
                'queue_name'    => $queue_name,
                'delay'         => $delay
            ], __METHOD__);
        } else {
            if(empty($this->queue_name)) {
                throw new ParamErrorException('model [QUEUE] param `queue_name` is require');
            }
            Console::debug("Pusher model is [QUEUE]", [
                'exchange'      => $exchange,
                'queue_name'    => $queue_name,
                'delay'         => $delay
            ], __METHOD__);
        }

        $this->connection = new AMQPStreamConnection(
            self::HOST,
            self::PORT,
            self::USER,
            self::PASS,
            $vhost = '/',
            $insist = false,
            $login_method = 'AMQPLAIN',
            $login_response = null,
            $locale = 'en_US',
            $connection_timeout = 3.0,
            $read_write_timeout = 10,
            $context = null,
            $keepalive = false,
            $heartbeat = 5
        );
        $this->channel = $this->connection->channel();
        if(0 == $this->delay) {
            $this->prepare();
        } else {
            $this->prepareTypeDelay();
        }


    }

    /**
     * set some declare not only include exchange or queue
     */
    public function prepare(){
        if($this->exchange) {
            $this->channel->exchange_declare(
                $this->exchange,
                Config::DEFAULT_EXCHANGE_TYPE,
                false,
                $durable= true,
                $auto_delete = false
            );
        } else {
            $this->channel->queue_declare(
                $this->queue_name,
                false,
                $durable= true, /* persistent queue */
                false,
                $auto_delete = false
            );
        }
    }

    /**
     * delay prepare
     */
    public function prepareTypeDelay(){
        //declare
        $this->channel->exchange_declare(
            $this->exchange,
            Config::DEFAULT_EXCHANGE_TYPE,
            false,
            $durable= true,
            $auto_delete = false
        );

    }

    /**
     * push task
     * @param $router_key
     * @param $payload
     */
    public function push($payload, $router_key = ''){
        if(0 == $this->delay) {
            $this->immediatePush($payload, $router_key);
        } else {
            $this->delayPush($payload, $router_key);
        }
        Console::debug("[Push][" . ($this->delay ? "Delay={$this->delay}":"Immediate" ) . "] ", [
            "message" => $payload,
            "router_key" => $router_key
        ]);
    }

    /**
     * push normal task
     * @param $payload
     * @param string $router_key
     */
    protected function immediatePush($payload, $router_key = ''){
        $msg = new AMQPMessage(
            $payload,
            [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,// persistent message
            ]);
        if($this->exchange) {
            $this->channel->basic_publish($msg, $this->exchange, $router_key);
        } else {
            $this->channel->basic_publish($msg, '', $this->queue_name);
        }
    }

    /**
     * push delay task
     * @param $router_key
     * @param $payload
     */
    protected function delayPush($payload, $router_key)
    {
        $delay_queue_name  = "delay_{$this->exchange}_{$router_key}_{$this->delay}";
        $delay_router_key  = "delay.{$this->exchange}.{$router_key}.{$this->delay}";

        $tale = new AMQPTable();
        $tale->set('x-dead-letter-exchange', $this->exchange);
        $tale->set('x-dead-letter-routing-key', $router_key);
        $tale->set('x-message-ttl', $this->delay * 1000);

        //for delay waiting
        $this->channel->queue_declare(
            $delay_queue_name,
            false,
            $durable = true,
            false,
            $auto_delete = false,
            false,
            $tale
        );
        $this->channel->queue_bind($delay_queue_name, $this->exchange, $delay_router_key);


        $msg = new AMQPMessage(
            $payload,
            $properties =[
//                'expiration' => intval($delay * 1000),// this param control message ttl alone, different expire  will cause queue blocked by long expire task
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,// persistent message
            ]);

        $this->channel->basic_publish(
            $msg,
            $this->exchange,
            $delay_router_key
        );

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