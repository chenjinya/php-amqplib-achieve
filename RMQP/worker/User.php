<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午7:56
 */
namespace RMQP\worker;
use RMQP\lib\Console;
use RMQP\worker\RMQPWorkerAbstract;


class User extends RMQPWorkerAbstract  {


    protected $router_key_list = [
        'a.b.c',
        'a.*',
    ];

}