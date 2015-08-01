<?php

namespace Naps\IO;

/**
 * 変数だと削除してしまうかもしれないし、スタティックで使う
 */
class DB
{
    /**
     * @var mysqli
     */
    private static $conn;
    /**
     * @var string[]
     */
    private static $config;

    
    /**
     * データベースへの接続設定をする
     * @param string      $db_host
     * @param string      $db_user
     * @param string      $db_pass
     * @param string      $db_name
     */
    public static function config($db_host, $db_user_, $db_pass, $db_name){
        self::$config = func_get_args();
    }


    /**
     * データベースサーバーに接続する
     * @return bool 接続に成功したかどうか
     */
    public static function connect(){
        self::$conn = new \mysqli(self::$config[0], self::$config[1], self::$config[2]);
        return !mysqli_connect_error();
    }

    /**
     * select_db()を行う
     * @return bool 接続に成功したかどうか
     */
    public static function open(){
        return self::$conn ? self::$conn->select_db(self::$config[3]) : false;
    }

    /**
     * データベースの処理を行う。
     *
     * @param callable $action 何らかの処理 (第1引数がデータベースのオブジェクト)
     * @param array    $args $actionに追加で渡したい値のリスト
     * @return mixed   $actionの返り値をそのまま返す
     */
    public static function action(callable $action, array $args=[]){
        return call_user_func($action, array_merge([self::$conn], $args));
    }
}

