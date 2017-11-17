<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午8:08
 */

namespace app\worker;
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
    protected $queue_name = '';
    public function __construct($queue_name){
        $this->connection = new AMQPStreamConnection(self::HOST, self::PORT, self::USER, self::PASS);
        $this->channel = $this->connection->channel();
        $this->queue_name = $queue_name;
        $this->prepare();
        Console::info("init success!", [
            "queue_name" => $queue_name,
            "host" => self::HOST,
            "port" => self::PORT,
        ], __METHOD__);
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
    public function listen(){
        $callback = function($msg) {
            $this->execute($msg);
        };

        //don't dispatch a new message to a worker until it has processed and acknowledged the previous one
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
            Console::info('listening...', [], __METHOD__);
            $this->channel->wait();
        }

    }

    public function execute($msg){

        //for acknowledgement, basic_consume's fourth param.
        //is not send ack, task will always retry by another worker
        //http://www.rabbitmq.com/tutorials/tutorial-two-php.html
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }
}