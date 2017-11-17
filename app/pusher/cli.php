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


$queueName = $argv[1];
$msgBody = $argv[2] ?  $argv[2] : 'Hello world!';
$fullClass = "app\\pusher\\{$queueName}";

$a = new $fullClass($queueName);
$a->push($msgBody);