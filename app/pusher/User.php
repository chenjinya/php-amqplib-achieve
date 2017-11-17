<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午7:56
 */
namespace app\pusher;

use app\lib\Console;


class User extends RMQPPusherAbstract   {

    public function __construct($topic, $queue_name, $option){
        parent::__construct($topic, $queue_name, $option);
    }
    public function push($payload, $router_key){
        Console::warning('', [$payload, $router_key]);
        parent::push($payload, $router_key);
    }

}