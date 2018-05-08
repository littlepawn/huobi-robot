<?php
// 定义参数
define('ACCOUNT_ID', '37129331'); // 你的账户ID 
define('ACCESS_KEY','xxxxxxxxxxxxxxxxxxxxxxxxxx'); // 你的ACCESS_KEY
define('SECRET_KEY', 'xxxxxxxxxxxxxxxxxxxxxxxxx'); // 你的SECRET_KEY
include "lib.php";
include "telegram.php";

$count=30;  //次数 可类比分钟

//支持的交易对
$symbol=[
    'BTC'=>'btcusdt',
    'EOS'=>'eosusdt',
    'ADA'=>'adausdt',
    'RUFF'=>'ruffusdt',
    'IOST'=>'iostusdt',
    'ETH'=>'ethusdt',
    'XRP'=>'xrpusdt',
];

$percent=[
    'BTC'=>[0.02,0.02],
    'EOS'=>[0.02,0.02],
    'ADA'=>[0.02,0.02],
    'RUFF'=>[0.02,0.02],
    'IOST'=>[0.02,0.02],
    'ETH'=>[0.02,0.02],
    'XRP'=>[0.02,0.02],
];

/**
 * 按种类处理数据
 */
function handleByType($symbol,$percent,$count){
    //实例化redis
    $redis = new Redis();
    //连接
    $redis->connect('127.0.0.1', 6379);

    //实例化类库
    $req = new req();

    foreach ($symbol as $k=>$v) {
        $res = $req->get_market_trade($v);
        if ($res->status == 'ok') {
            $data = $res->tick->data;
            $text = '';

            $currentPrice = 0;
            foreach ($data as $value) {
                $text .= '========' . $k . '========' . "\n" . ' 价格: ' . $value->price . "\n" . ' 交易方式: ' . $value->direction . "\n" . ' 交易时间: ' . date('Y-m-d H:i:s', $value->ts / 1000 + 3600 * 8) . "\n";

                //更新当前价格
                if ($currentPrice) {
                    $currentPrice = $currentPrice > $value->price ? $value->price : $currentPrice;
                } else {
                    $currentPrice = $value->price;
                }
            }

            $priceList = $redis->lRange($k, 0, -1);
            $lastPrice = $priceList[0]; //上次查询的价格
            sort($priceList);
            $minPrice = $priceList[0];      //30次内最小值
            $maxPrice = $priceList[count($priceList) - 1];  //30次内最大值

            //下跌警报
            if (bccomp($currentPrice,$lastPrice,6)<0) {
                $diff = bcdiv(abs(bcsub($currentPrice,$maxPrice,6)), $maxPrice, 6);
                echo $k.'==========下跌比率========'.$diff."\n";
                if (bccomp($diff,$percent[$k][0],6)>=0) {
                    $telegram = new Telegram();
                    if($k=='EOS'||$k=='ETH'||$k=='XRP'){
                        $response = $telegram->sendMessage('=========下跌' . $diff . '===========' . "\n" . $text,true);
                    }else {
                        $response = $telegram->sendMessage('=========下跌' . $diff . '===========' . "\n" . $text);
                    }
//                    var_dump($response);
                }
            }
            //上涨报告
            if (bccomp($currentPrice,$lastPrice,6)>0) {
                $diff = bcdiv(abs(bcsub($currentPrice,$minPrice,6)), $minPrice, 6);
                echo $k.'===========上涨比率=========='.$diff."\n";
                if (bccomp($diff , $percent[$k][1],6)>=0) {
                    $telegram = new Telegram();
                    if($k=='EOS'||$k=='ETH'||$k=='XRP'){
                        $response = $telegram->sendMessage('=========上涨' . $diff . '===========' . "\n" . $text,true);
                    }else {
                        $response = $telegram->sendMessage('=========上涨' . $diff . '===========' . "\n" . $text);
                    }
//                    var_dump($response);
                }
            }

            $redis->lPush($k, $currentPrice);
            $length = $redis->lLen($k);
            if ($length > $count) {
                $redis->rPop($k);
            }

            echo 'all ok!!!!!!!!!' . PHP_EOL;
        } else {
            echo 'get info failed!!!!!!!!!' . PHP_EOL;
        }
    }
}

function microtime_format($tag, $time){
   list($usec, $sec) = explode(".", $time);
   $date = date($tag,$usec);
   return str_replace('x', $sec, $date);
}

handleByType($symbol,$percent,$count);
?>
