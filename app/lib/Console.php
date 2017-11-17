<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午7:56
 */
namespace app\lib;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Console {

    protected  $log = null;
    protected  $category = null;

    public static function info($message , $payload = [], $category = 'default'){
        $log = new Logger($category);
        $log->info($message, $payload);
    }

    public static function warning($message , $payload = [], $category = 'default'){
        $log = new Logger($category);
        $log->warning($message, $payload);
    }

    public static function error($message , $payload = [], $category = 'default'){
        $log = new Logger($category);
        $log->error($message, $payload);
    }

    public static function notice($message , $payload = [], $category = 'default'){
        $log = new Logger($category);
        $log->notice($message, $payload);
    }


}