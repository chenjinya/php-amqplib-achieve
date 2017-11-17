<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午7:56
 */
namespace app\worker;
use app\lib\Console;
use app\worker\RMQPWorkerAbstract;


class User extends RMQPWorkerAbstract  {

    const MQ_HOST = 'localhost';
    const MQ_PORT = 5672;
    const MQ_USER = 'guest';

    public function __construct($queue_name){
        parent::__construct($queue_name);
    }
    public function execute( $msg){
        Console::warning('dd', (array)$msg);
        $this->handelHello($msg);
        parent::execute($msg);
    }

    protected function handelHello($msg){
        Console::warning($msg->body);
        sleep(10);
    }
}