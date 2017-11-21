<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午7:56
 */
namespace RMQP\worker;

use RMQP\lib\RMQPWorkerAbstract;

class Test extends RMQPWorkerAbstract   {

    protected function getRouterKeys()
    {
        return [
            'a.b.c',
            'a.*',
        ];
    }
}