<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午7:57
 */
namespace app\pusher;
use app\lib\Console;

require_once __DIR__ . '/../../vendor/autoload.php';

require_once __DIR__ . '/../autoload.php';

$topic = '';
$queue_name = '';
$message = '';
$option = [];
$router_key = '';
$delay = 0;
$exchange = '';
$arguments = array_slice($argv, 1);
$help = "Usage: [--help][--topic=string][--exchange=string][--queue=string][--router=string][--message=string][--delay=number] \n";

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
            break;
        case '--topic':
            $topic = $arr[1];
            break;
        case '--exchange':
            $exchange = $arr[1];
            break;
        case '--queue':
            $queue_name = $arr[1];
            break;
        case '--router':
            $router_key = $arr[1];
            break;
        case '--message':
            $message = $arr[1];
            break;
        case '--delay':
            $delay = $arr[1];
            break;
        default :
            echo $help;
            exit(0);
    }
}


$fullClass = "app\\pusher\\{$topic}";

$a = new $fullClass($exchange, $queue_name, $delay);
if($delay) {
    $a->delayPush($message,$delay, $router_key);
} else {
    $a->push($message, $router_key);

}

