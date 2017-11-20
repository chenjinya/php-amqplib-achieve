<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午8:08
 */

namespace RMQP\worker;


interface RMQPWorkerInterface
{

    public function __construct($topic, $queue_name, $option);
    public function listen();
    public function prepare();
    public function execute($msg);


}