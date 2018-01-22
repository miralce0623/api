<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/20 0020
 * Time: 17:46
 */

class File
{
    private $_dir;
    const EXT = '.txt';

    public function __construct()
    {
        $this->_dir = dirname(__FILE__).'/files/';
    }

    public function cacheData($key, $value, $cacheTime = 0){
        $filename = $this->_dir . $key . self::EXT;

        if($value !== ''){
            $dir = dirname($filename);
            if(!is_dir($dir)){
                mkdir($dir,0777);
            }

            $cacheTime = sprintf('%011d',$cacheTime);

            return file_put_contents($filename,$cacheTime.json_encode($value));
        }

        if(is_file($filename)){
            return false;
        }
        $content = file_get_contents($filename);
        $content = substr($content,11);

        return json_decode($content,true);



    }
}