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
    const DEFAULT_EXCHANGE_TYPE = Config::DEFAULT_EXCHANGE_TYPE;


    protected  $connection = null;
    protected  $channel = null;

    protected $queue_name = '';
    protected $router_key = '';
    protected $delay = 0;
    protected $option = [];
    protected $exchange = false;
    protected $delay_exchange_name = '';
    protected $delay_queue_name = '';

    /**
     * RMQPPusherAbstract constructor.
     * @param $exchange
     * @param $queue_name
     * @param $delay
     * @throws ParamErrorException
     */
    public function __construct($exchange = '', $queue_name = '', $delay = 0){

        $this->exchange = $exchange;
        $this->queue_name = $queue_name;

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

        $this->connection = new AMQPStreamConnection(self::HOST, self::PORT, self::USER, self::PASS);
        $this->channel = $this->connection->channel();
        if(0 == $delay) {
            $this->prepare();
        } else {
            $this->prepareTypeDelay($delay);
        }


    }

    /**
     * set some declare not only include exchange or queue
     */
    public function prepare(){
        if($this->exchange) {
            $this->channel->exchange_declare($this->exchange, self::DEFAULT_EXCHANGE_TYPE, false, false, false);
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
     * delay prepare
     * @param $delay
     */
    public function prepareTypeDelay($delay){

        $this->delay                = $delay;
        $delay_exchange_name        = "{$this->exchange}_delay_{$this->delay}";
        $this->delay_exchange_name  = $delay_exchange_name;


        $this->channel->exchange_declare($delay_exchange_name , Config::DELAY_EXCHANGE_TYPE,false,false,false);
        $this->channel->exchange_declare($this->exchange, self::DEFAULT_EXCHANGE_TYPE,false,false,false);


        //actually task queue
        $this->channel->queue_declare($this->queue_name, false,true,false,false,false);
        $this->channel->queue_bind($this->queue_name, $this->exchange,  $this->exchange);
    }

    /**
     * push task
     * @param $router_key
     * @param $payload
     */
    public function push($payload, $router_key = ''){

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
     * @param $delay
     */
    public function delayPush($payload, $delay, $router_key)
    {
        $delay = intval($delay);
        $delay_queue_name           = "{$this->queue_name}_{$router_key}_delay_{$this->delay}";
        $this->delay_queue_name     = $delay_queue_name;

        $tale = new AMQPTable();
        $tale->set('x-dead-letter-exchange', $this->exchange);
        $tale->set('x-dead-letter-routing-key', $router_key);
        $tale->set('x-message-ttl', $delay * 1000);

        //for delay waiting
        $this->channel->queue_declare($delay_queue_name,false,$durable = true,false,false,false,$tale);
        $this->channel->queue_bind($delay_queue_name, $this->delay_exchange_name, $router_key);


        $msg = new AMQPMessage(
            $payload,
            $properties =[
//                'expiration' => intval($delay * 1000),// this param control message ttl alone, different expire  will cause queue blocked by long expire task
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,// persistent message
            ]);

        $this->channel->basic_publish(
            $msg,
            $this->delay_exchange_name,
            $router_key
        );
        Console::debug("[Pusher][Delay][$delay] $payload", $properties);

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