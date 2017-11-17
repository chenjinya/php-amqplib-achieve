<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午7:57
 */
namespace app\worker;
require_once __DIR__ . '/../autoload.php';

$topic = '';
$queue_name = '';
$option = '';
$arguments = array_slice($argv, 1);
foreach($arguments as $cmd) {
    $arr = explode('=', $cmd);
    if(empty($arr) || count($arr) < 1) {
        $arr[0] = false;
    }
    switch ($arr[0]) {
        case false:
        case '--help':
            echo "Usage: [--help][--topic=some][--queue=some][--subscribe] \n";
            exit(0);
            break;
        case '--topic':
            $topic = $arr[1];
            break;
        case '--queue':
            $queue_name = $arr[1];
            break;
        case '--subscribe':
            $option['subscribe'] = true;
            break;
    }
}


$fullClass = "app\\worker\\{$topic}";

$a = new $fullClass($topic, $queue_name, $option);
$a->listen();
