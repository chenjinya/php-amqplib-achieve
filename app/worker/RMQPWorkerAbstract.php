<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午8:08
 */

namespace app\worker;
use app\worker\exception\EmptyRouterException;
use app\worker\exception\ParamErrorException;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use app\lib\Console;


class RMQPWorkerAbstract implements RMQPWorkerInterface
{

    const HOST = 'localhost';
    const PORT = 5672;
    const USER = 'guest';
    const PASS = 'guest';
    protected $connection = null;
    protected $channel = null;
    protected $option = [];
    protected $topic = '';
    protected $queue_name = '';
    protected $router_key = '';
    protected $router_key_list = [];
    protected $is_subscribe = false;

    /**
     * RMQPWorkerAbstract constructor.
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
            Console::info("worker model is [SUBSCRIBE]", [], __METHOD__);
        } else {
            if(empty($this->queue_name)) {
                throw new ParamErrorException('model [QUEUE] param `queue_name` is require');
            }
            Console::info("worker model is [QUEUE]", [], __METHOD__);
        }

        $this->connection = new AMQPStreamConnection(self::HOST, self::PORT, self::USER, self::PASS);
        $this->channel = $this->connection->channel();

        $this->prepare();
        Console::info("init success!", [
            "topic" => $topic,
            "host" => self::HOST,
            "port" => self::PORT,
        ], __METHOD__);
    }


    /**
     * set some declare not only include exchange or queue
     * @throws EmptyRouterException
     */
    public function prepare(){

        if($this->is_subscribe == true) {
            $this->channel->exchange_declare($this->topic, 'topic', false, false, false);
            list($queue_name, ,) = $this->channel->queue_declare(
                '',
                false,
                $durable= true, /* persistent queue */
                false,
                false
            );
            $this->queue_name = $queue_name;

            if(empty($this->router_key_list)) {
                throw new EmptyRouterException("Router key list should not be EMPTY!");
            }
            foreach($this->router_key_list as $router_key) {
                Console::info("Queue bind: {$this->queue_name} => $router_key");
                $this->channel->queue_bind($this->queue_name, $this->topic, $router_key);
            }
        } else {
            Console::info("Queue name: {$this->topic}");
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
                Console::error("Execute return FALSE, task exec FAILED,  attention to queue ACK");
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