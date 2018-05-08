<?php

//实例化redis
$redis = new Redis();
//连接
$redis->connect('127.0.0.1', 6379);

$keys=['BTC','EOS','ADA','RUFF','IOST',];


foreach ($keys as $value){
//    $redis->delete($value);
    $length=$redis->lLen($value);
    echo $value.'======'.$length.PHP_EOL;

    $list=$redis->lRange($value,0,-1);
    print_r($list);
}