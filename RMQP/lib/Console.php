<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午7:56
 */
namespace RMQP\lib;

use Monolog\Logger;

class Console {

    const DEFAULT_CATEGORY = 'app';
    protected  $log = null;
    protected  $category = null;

    public static function info($message , $payload = [], $category = self::DEFAULT_CATEGORY){
        $log = new Logger($category);
        $log->info($message, $payload);
    }

    public static function warning($message , $payload = [], $category = self::DEFAULT_CATEGORY){
        $log = new Logger($category);
        $log->warning($message, $payload);
    }

    public static function error($message , $payload = [], $category = self::DEFAULT_CATEGORY){
        $log = new Logger($category);
        $log->error($message, $payload);
    }

    public static function notice($message , $payload = [], $category = self::DEFAULT_CATEGORY){
        $log = new Logger($category);
        $log->notice($message, $payload);
    }

    public static function debug($message , $payload = [], $category = self::DEFAULT_CATEGORY){
        if(Config::ENV == 'dev') {
            $log = new Logger($category);
            $log->debug($message, $payload);
        }

    }


}