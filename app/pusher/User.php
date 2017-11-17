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

    public function __construct($queue_name){
        parent::__construct($queue_name);
    }
    public function push($payload){
        parent::push($payload);
    }


}