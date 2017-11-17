<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午8:08
 */

namespace app\pusher;


interface RMQPPusherInterface
{

    public function __construct($topic, $queue_name, $option);
    public function push($payload, $router_key);
    public function delayPush($payload, $delay, $router_key);
}