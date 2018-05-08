<?php
require 'vendor/autoload.php';
use Telegram\Bot\Api;

class Telegram{
    public function getInfo(){
        try {
            $telegram = new Api('597007058:AAHiQXbCDYPtfuX4qyXlxPu9UVo0eghpSKc');
            $response = $telegram->getMe();
            $botId = $response->getId();
            $firstName = $response->getFirstName();
            $username = $response->getUsername();
            echo 'id: ' . $botId . ' firstName: ' . $firstName . ' username: ' . $username;
        }catch (Exception $e){
            echo $e->getMessage();
        }
    }

    public function sendMessage($text='hello',$flag=false){
        try {
            $telegram = new Api('597007058:AAHiQXbCDYPtfuX4qyXlxPu9UVo0eghpSKc');
            $params=[
                'chat_id'=>'492648647',
                'text'=>$text,
            ];
            $response = $telegram->sendMessage($params);
            if($flag){
                $params=[
                    'chat_id'=>'389098296',
                    'text'=>$text,
                ];
                $telegram->sendMessage($params);
                $params=[
                    'chat_id'=>'506568231',
                    'text'=>$text,
                ];
                $telegram->sendMessage($params);
            }
            return $response;
        }catch (Exception $e){
            echo $e->getMessage();
            return false;
        }
    }

    public function sendOrderMessage($text='hello',$chatId){
        try {
            $telegram = new Api('597007058:AAHiQXbCDYPtfuX4qyXlxPu9UVo0eghpSKc');
            $params=[
                'chat_id'=>$chatId,
                'text'=>$text,
            ];
            $response = $telegram->sendMessage($params);
            return $response;
        }catch (Exception $e){
            echo $e->getMessage();
            return false;
        }
    }
}
?>