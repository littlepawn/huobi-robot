<?php
include "telegram.php";

$servername = "114.215.100.111";
$username = "telnet";
$password = "gcTG0EWhfmYoUnp4Kj";
$database='db_work';

$idArr=[
    36=>'389098296',
    117=>'492648647',
    181=>'506568231',
];


try {
    // 创建连接
    $conn = new mysqli($servername, $username, $password, $database);

    // 检测连接
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }
//    echo "连接成功";
    $sql="set names utf8";
    $conn->query($sql);

    $sql = "SELECT * FROM kq_order where user_id in (117,36,181) and order_date=".strtotime(date('Y-m-d'));
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // 输出数据
        while($row = $result->fetch_assoc()) {
            echo "Uid: " . $row["user_id"]. " - Food: " . $row["food_name"]."<br>";
            unset($idArr[$row['user_id']]);
        }
    } else {
        echo "0 结果".PHP_EOL;
    }

    $telegram = new Telegram();
    foreach ($idArr as $value){
        $text='快点饭，傻屌!!!!!!!!!!!!!';
        $response = $telegram->sendOrderMessage($text,$value);
        var_dump($response);
    }
}catch (Exception $e){
    echo $e->getMessage();
}

$conn->close();

