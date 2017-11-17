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

//    public function execute();
//    public function beforeExecute();
//    public function afterExecute();


    public function push($payload);
    public function delayPush($payload, $delay);
}