<?php

use Naps\IO\DB;
use Naps\IO\DBException;
use Naps\PhpManual\Data;
use Naps\Qphp\Cache;


/**
 * エラーが発生したときはエラー情報をjsonで出力する
 */
set_error_handler(function($errno, $errstr, $errfile, $errline){
    json([
        'error'=>$errno,
        'message'=>"$errstr in $errfile at $errline"
    ]);
});


/**
 * json形式で出力して処理を終える
 * @param array $data jsonにするデータの配列
 * ※この関数は終了を含む
 */
function json(array $data){
    header('Content-Type: text/application+json; charset=utf-8');
    if(!isset($data['error'])) $data['error'] = 0;
    echo json_encode($data);
    die;
}


/**
 * @var string QPHPのトップディレクトリ
 */
define('QPHP_ROOT_DIR', dirname(dirname(__DIR__)));

/**
 * @var string DIRECTOR_SEPARATORの短縮
 */
define('DS', DIRECTOR_SEPARATOR);


/**
 * クラスファイルをロードする方法
 */
spl_autoload_register(function($class_name){
    $file = __DIR__ . '/classes/' . strtr($class_name, '\\', DIRECTOR_SEPARATOR) . '.php';
    if(is_readable($file)) require_once $file;
});

/**
 * 設定は設定ファイルでできる
 */
require_once __DIR__ . '/config.php';


/**
 * データベース接続
 */
if(!DB::connect()) die('ERROR: cannot connect the database');
if(!DB::open()){
    // データベースの定義
    DB::action(function($db){
        $db->query('CREATE DATABASE naps_php');
    });
    if(!DB::open()) die('ERROR: cannot create the database');
}


/**
 * @var string マニュアルファイルのディレクトリ
 */
Data::$dir = QPHP_ROOT_DIR . DS . 'php-chunked-xhtml';


/**
 * 検索処理
 *
 * ※$max_retryで再施行に上限があるので留意
 */
for($current=0, $max_retry=5; $current<$max_retry; $current++){
    try{
        $data_list = Cache::find(isset($_GET['q']) ? $_GET['q'] : '');
        break;
    }catch(DBException $e){
        $db_errno = DB::action(function($db){ return $db->errno; });
        // テーブルが存在しないエラーならテーブルを定義する
        if(in_array($db_errno, []) && DB::initialize() && Cache::build()){
            continue;
        }
        // 別のエラーか失敗したならjsonでエラーを伝える
        else{
            json([
                'error'=>$db_error,
                'message'=>DB::action(function($db){ return $db->error; });
            ]);
        }
    }
}


// 必要な情報だけを出力する
foreach($data_list as $i=>$data){
    $data_list[$i] = [
        'name'=>$data->name,
        'file'=>basename($data->file),
        'type'=>$data->getTypeName()
    ];
}

json([
    'error'=>0,
    'message'=>'ok',
    'items'=>$data_list
]);


