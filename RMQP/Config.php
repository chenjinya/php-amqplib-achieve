<?php
/**
 * Created by PhpStorm.
 * User: jinya
 * Date: 2017/11/16
 * Time: 下午7:57
 */
namespace RMQP;

class Config
{
    const HOST = 'localhost';
    const PORT = 5672;
    const USER = 'guest';
    const PASS = 'guest';

    const DEFAULT_EXCHANGE_TYPE = 'topic';

    //delay use   `x-dead-letter-router-key`, it's a one-to-one router, so we use `direct` type
    const DELAY_EXCHANGE_TYPE = 'direct';

    const ENV = 'dev';
}
