<?php
namespace Naps\Qphp;

use Naps\PhpManual\Data;

/**
 * ファイルではなく、処理用に管理するデータベース
 */
class Cache
{
    /**
     * テーブルを作成する
     * @return bool
     */
    public static function initalize(){
        return DB::action(function($db){
            return $db->query(
                'CREATE TABLE IF NOT EXISTS data ('.
                    'id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,'.
                    'slug char(255) not null,'.
                    'name char(255) not null,'.
                    'type tinyint,'.
                    'text TEXT,'.
                    'primary key (id),'.
                    'index(slug, name, type)'.
                ')ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1');
        });
    }

    /**
     * データベースを構築する
     * @return bool
     */
    public static function build(){
        $items = scandir(Data::$dir);
        foreach($items as $item){
            // *.htmlだけが対象
            if(strpos($item, -5) !== '.html') continue;
            // データとして吸いだして処理
            $data = new Data;
            $data->parseFile(Data::$dir . '/' . $item);
            if(!$this->add($data)) return false;
            unset($data);
        }
        return true;
    }

    /**
     * データを追加する
     *
     * @param Data $data マニュアルデータ1件
     * @return bool
     */
    public static function add(Data $data){
        return DB::action(function($db, Data $data){
            return $db->query(
                sprintf(
                    "insert into data (slug, name, text, type)values('%s', '%s', '%s', %s)",
                    $db->real_escape_string($data->slug),
                    $db->real_escape_string($data->name),
                    $db->real_escape_string($data->text),
                    $data->type
            ));
        }, [$data]);
    }

    /**
     * データを検索する
     *
     * @param string $keyword 検索キーワードひとつ
     * @return Data[]
     */
    public static function find($keyword){
        return DB::action(function($db, $keyword){
            $like = '%' . $db->real_escape_string($keyword) . '%';
            $result = $db->query(
                sprintf(
                    "select slug, name, text, type from data ".
                        "where name like '%s' or text like '%s' order by type asc",
                    $like,
                    $like
            ));

            $rows = [];
            while($row = $result->fetch_assoc()){
                $rows[] = new Data($row['slug'], $row['name'], (int)$row['type'], $row['text']);
            }
            return $rows;
        }, [$keyword]);
    }
}

