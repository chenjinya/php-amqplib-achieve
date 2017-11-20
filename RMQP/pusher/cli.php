<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午7:57
 */
namespace app\pusher;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../autoload.php';


$arguments = array_slice($argv, 1);
$help = "Usage: [--help][--topic=string][--exchange=string][--queue=string][--router=string][--message=string][--delay=number] \nFor detail please read README\n";

$param = [];
$paramAllow = [
    '--help',
    '--topic',
    '--exchange',
    '--queue',
    '--router',
    '--message',
    '--delay',
];
foreach($arguments as $cmd) {
    $arr = explode('=', $cmd);
    if(empty($arr) || count($arr) <= 1) {
        echo $help;exit(0);
    }

    if(in_array($arr[0], $paramAllow)) {
        $param[$arr[0]] = $arr[1];
    } else {
        echo "Param {$arr[0]} is illegal";
        echo $help;exit(0);

    }

}
$class_name = $param['--topic'];
$queue_name = $param['--queue'];
$exchange = $param['--exchange'];
$router_key = $param['--router'];
$message = $param['--message'];
$delay = $param['--delay'];

$fullClass = "RMQP\\pusher\\{$class_name}";

$a = new $fullClass($exchange, $queue_name, $delay);
if($delay) {
    $a->delayPush($message,$delay, $router_key);
} else {
    $a->push($message, $router_key);

}

