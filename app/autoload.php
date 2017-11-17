<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午7:57
 */
require_once __DIR__ . '/../vendor/autoload.php';

spl_autoload_register(function($class) {
    list($alias, $dir, $class) = explode('\\', $class);
    if ($alias === 'app') {
        $class_file = __DIR__.  "/{$dir}/{$class}.php";
        if (file_exists($class_file)) {
            require_once($class_file);
        } else {
            throw new \Exception("[ERROR] Autoload class: {$class} failed![File {$class_file} not exists");
        }
    } else {
        var_export([$alias, $dir, $class]);
        throw new \Exception("[ERROR] Autoload class: {$class} failed![alias={$alias},dir={$dir}");
    }
});
