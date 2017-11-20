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


    protected $router_key_list = [
        'a.b.c',
        'a.*',
    ];

}