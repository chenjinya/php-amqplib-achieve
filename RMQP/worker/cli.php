<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午7:57
 */
namespace RMQP\worker;
require_once __DIR__ . '/../autoload.php';

$topic = '';
$queue_name = '';
$exchange = '';
$delay = 0;
$arguments = array_slice($argv, 1);
$help = "Usage: [--help][--topic=string][--exchange=string][--queue=string][--delay=number] \n";
foreach($arguments as $cmd) {
    $arr = explode('=', $cmd);
    if(empty($arr) || count($arr) < 1) {
        $arr[0] = false;
    }
    switch ($arr[0]) {
        case false:
        case '--help':
            echo $help;
            exit(0);
        case '--topic':
            $topic = $arr[1];
            break;
        case '--queue':
            $queue_name = $arr[1];
            break;
        case '--exchange':
            $exchange = $arr[1];
            break;
        case '--delay':
            $delay = $arr[1];
            break;
        default:
            echo $help;
            exit(0);
    }
}


$fullClass = "RMQP\\worker\\{$topic}";

$a = new $fullClass($exchange, $queue_name, $delay);
$a->listen();
