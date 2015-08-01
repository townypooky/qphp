<?php
namespace Naps\PhpManual;

/**
 * マニュアルデータの1件
 */
class Data
{
    public static $dir = '.';
    public static $typeNames = [
        self::TYPE_NONE => 'none',
        self::TYPE_INDEX => 'index',
        self::TYPE_FUNCTION => 'function',
        self::TYPE_CLASS => 'class',
        self::TYPE_MEMBER => 'member',
        self::TYPE_ETC => 'etc'
    ];

    public $slug;
    public $name;
    public $type;
    public $text;

    const TYPE_NONE = -1;
    const TYPE_INDEX = 0;
    const TYPE_FUNCTION = 1;
    const TYPE_CLASS = 2;
    const TYPE_MEMBER = 3;
    const TYPE_ETC = 4;



    public function __construct($slug=null, $name=null, $type=self::TYPE_NONE, $text=null){
        $this->slug = $slug;
        $this->name = $name;
        $this->type = $type;
        $this->text = $text;
    }

    public function __get($varname){
        if(isset($this->$varname)){
            return $this->$varname;
        }else if($varname === 'file'){
            if(!is_string($this->slug)) return $this->slug;
            return self::$dir . '/' . $this->slug . '.html';
        }
    }

    /**
     * @return string $typeの名前
     */
    public function getTypeName(){
        return self::$typeNames[$this->type];
    }

    /**
     * @param string $file ファイルパス
     */
    public function parseFile($file){
        $content = file_get_contents($file, false);

        $this->slug = null;
        $this->name = null;
        $this->type = self::TYPE_NONE;
        $this->text = null;

        // $slug
        $this->slug = basename($file, '.html');

        // $name
        $prefix = '<h1 class="refname">';
        $i = strpos($content, $prefix);
        if(is_int($i)){
            $j = strpos($content, '</h1>');
            if(is_int($j)){
                $n = $i + strlen($prefix);
                $this->name = substr($content, $n, $j - $n);
            }else{
                return; // エラー
            }
        }else{
            return; // エラー
        }


        // $type
        $digits = explode('.', $this->slug);
        if(!isset($digits[1])){
            $this->type = self::TYPE_INDEX;
        }else{
            switch($digits[0]){
                case 'function':
                    $this->type = self::TYPE_FUNCTION;
                    break;
                case 'class':
                    $this->type = self::TYPE_CLASS;
                    break;
                default:
                    // memberの可能性
                    $refname = $digits[0] . '::';
                    for($i=1, $k=count($digits); $i<$k; $i++){
                        $refname = strtr($digits[$i], '-', '_');
                    }
                    if($this->name === $refname){
                        $this->type = self::TYPE_MEMBER;
                        break;
                    }
                    // 他はすべてetc
                    $this->type = self::TYPE_ETC;
                    break;
            }
        }

        // $textを取得する
        $this->text = preg_replace('/<[^>]+>/i', '', $content);
    }
}

