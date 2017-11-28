<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午8:08
 */

namespace RMQP\lib;
use PhpAmqpLib\Connection\AMQPStreamConnection;

abstract class RMQPWorkerAbstract implements RMQPWorkerInterface
{

    const HOST = Config::HOST;
    const PORT = Config::PORT;
    const USER = Config::USER;
    const PASS = Config::PASS;

    protected $connection = null;
    protected $channel = null;

    protected $exchange = '';
    protected $queue_name = '';
    protected $delay = 0;

    abstract protected function getRouterKeys();

    /**
     * RMQPWorkerAbstract constructor.
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
            Console::debug("Worker model is [EXCHANGE]", [
                'exchange'      => $exchange,
                'queue_name'    => $queue_name,
                'delay'         => $delay
            ], __METHOD__);
        } else {
            if(empty($this->queue_name)) {
                throw new ParamErrorException('model [QUEUE] param `queue_name` is require');
            }
            Console::debug("Worker model is [QUEUE]", [
                'exchange'      => $exchange,
                'queue_name'    => $queue_name,
                'delay'         => $delay
            ], __METHOD__);
        }


        $this->connection = new AMQPStreamConnection(self::HOST, self::PORT, self::USER, self::PASS);
        $this->channel = $this->connection->channel();

        if(0 == $this->delay) {
            $this->prepare();

        } else {
            $this->prepareTypeDelay();
        }
        Console::debug("Worker start success!", [
            "exchange" => $exchange,
            "queue_name" => $queue_name,
            "host" => self::HOST,
            "port" => self::PORT,
        ], __METHOD__);

    }


    /**
     * set some declare not only include exchange or queue
     * @throws EmptyRouterException
     */
    public function prepare(){

        if($this->exchange == true) {
            $this->channel->exchange_declare(
                $this->exchange,
                Config::DEFAULT_EXCHANGE_TYPE,
                false,
                $durable= true,
                false
            );
            list($queue_name, ,) = $this->channel->queue_declare(
                $this->queue_name,
                false,
                $durable= true, /* persistent queue */
                false,
                $auto_delete = false
            );
            if(empty($this->queue_name)) {
                $this->queue_name = $queue_name;
            }

            $router_key_list = $this->getRouterKeys();
            if(empty($router_key_list)) {
                throw new EmptyRouterException("Router key list should not be EMPTY!");
            }

            foreach($router_key_list as $router_key) {
                $this->channel->queue_bind($this->queue_name, $this->exchange, $router_key);
            }
            Console::debug("Queue router bind: {$this->queue_name} => ",$router_key_list, __METHOD__);
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

    public function prepareTypeDelay(){


        $this->channel->exchange_declare(
            $this->exchange,
            Config::DEFAULT_EXCHANGE_TYPE,
            false,
            $durable = true,
            $auto_delete = false
        );
        $this->channel->queue_declare(
            $this->queue_name,
            false,
            $durable = true,
            false,
            $auto_delete = false
        );

        $router_key_list = $this->getRouterKeys();
        if(empty($router_key_list)) {
            throw new EmptyRouterException("Router key list should not be EMPTY!");
        }

        foreach($router_key_list as $router_key) {
            $this->channel->queue_bind($this->queue_name, $this->exchange, $router_key);
        }
        Console::debug("Queue router bind: {$this->queue_name} => ",$router_key_list, __METHOD__);
    }

    /**
     * listening
     */
    public function listen(){
        $callback = function($msg) {
            $ret = $this->execute($msg);
            if($ret) {
                // for acknowledgement, basic_consume's fourth param.
                // is not send ack, task will always retry by another worker
                // http://www.rabbitmq.com/tutorials/tutorial-two-php.html
                // show ack status: sudo rabbitmqctl list_queues name messages_ready messages_unacknowledged
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            } else {
                Console::error("Execute return FALSE, task exec FAILED,  attention to queue ACK, Send nack");
                $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag'], false, $requeue = true);
            }
        };

        // don't dispatch a new message to a worker until it has processed and acknowledged the previous one
        $this->channel->basic_qos(null, 1, null);

        $this->channel->basic_consume(
            $this->queue_name,
            '',
            false,
            $no_ack = false, /* processed need ack */
            false,
            false,
            $callback);
        while(count($this->channel->callbacks)) {
            $this->channel->wait();
        }

    }

    /**
     * @param $msg
     * @return true;
     */
    public function execute($msg){

        Console::debug("Get message ", [$msg->body], __METHOD__);
        return true;
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