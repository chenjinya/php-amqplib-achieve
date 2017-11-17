<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午9:21
 */
//echo phpinfo();;
//require_once __DIR__ . '/./vendor/autoload.php';

require_once  'app/autoload.php';

$u = new \app\publish\User();
$u->run();