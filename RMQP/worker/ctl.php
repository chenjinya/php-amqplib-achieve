<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午8:08
 */

namespace RMQP\worker;
require_once __DIR__ . '/../autoload.php';

use RMQP\worker\exception\EmptyRouterException;
use RMQP\worker\exception\ParamErrorException;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use RMQP\lib\Console;
use RMQP\Config;

class Control
{


    const HOST = Config::HOST;
    const PORT = Config::PORT;
    const USER = Config::USER;
    const PASS = Config::PASS;


    protected $connection = null;
    public $channel = null;

    /**
     * RMQPWorkerAbstract constructor.
     * @param $exchange
     * @param $queue_name
     * @param $delay
     * @throws ParamErrorException
     */
    public function __construct($exchange = '', $queue_name = '', $delay = 0){
        $this->connection = new AMQPStreamConnection(self::HOST, self::PORT, self::USER, self::PASS);
        $this->channel = $this->connection->channel();
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

$c = new Control();

$c->channel->queue_delete('user_delay_5');
$c->channel->queue_delete('user_delay_20');
$c->channel->queue_delete('user_delay_4');
$c->channel->queue_delete('user_delay_9');


$c->channel->queue_delete('user');

$c->channel->exchange_delete('user');
$c->channel->exchange_delete('user_delay_20');
$c->channel->exchange_delete('user_delay_4');
$c->channel->exchange_delete('user_delay_5');
$c->channel->exchange_delete('user_delay_9');