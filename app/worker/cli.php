<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午7:57
 */
namespace app\worker;
require_once __DIR__ . '/../autoload.php';

$queueName = $argv[1];
$fullClass = "app\\worker\\{$queueName}";

$a = new $fullClass($queueName);
$a->listen();
