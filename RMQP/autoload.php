<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午7:57
 */
require_once __DIR__ . '/../vendor/autoload.php';

spl_autoload_register(function($class) {

    $list = explode('\\', $class);
    $alias = $list[0];

    array_shift($list);
    $relative_file_path = implode("/", $list);

    if ($alias === 'RMQP') {
        $class_file = __DIR__.  "/{$relative_file_path}.php";
        if (file_exists($class_file)) {
            require_once($class_file);
        } else {
            throw new \Exception("[ERROR] Autoload class: {$class} failed![File {$class_file} not exists");
        }
    } else {
//        var_export([$alias, $dir, $class]);
        throw new \Exception("[ERROR] Autoload class: {$class} failed![alias={$alias},relative_file_path={$relative_file_path}");
    }
});
