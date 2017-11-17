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

    protected $router_key_list = [
        'a.b.c',
        'a.*',
    ];
    public function __construct($topic, $queue_name, $option){
        parent::__construct($topic, $queue_name, $option);
    }
    public function execute( $msg){

        $this->handelHello($msg);
        return true;
    }

    protected function handelHello($msg){
        Console::warning("execute " . $msg->body);
        sleep(10);
        Console::warning("execute finish");
    }
}