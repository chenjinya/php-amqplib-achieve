<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午8:08
 */

namespace RMQP\lib;


interface RMQPPusherInterface
{
    public function __construct($topic, $queue_name, $option);
    public function prepare();
    public function prepareTypeDelay();
    public function push($payload, $router_key);
}