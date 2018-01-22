<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/20 0020
 * Time: 16:50
 */

//require './response.php';
require './file.php';

$data = array(
    'id' => 1,
    'title' => '测试app接口返回json数据',
    'imgList' => array(
        'thumb' => '11.jpg',
        'img_100x100' => '11_100x100.jpg',
        'test' => array(1,2,3,5)
    )
);

//Response::json(200,'成功获取数据',$data);
//Response::show(200,'成功获取数据',$data);

$file = new File();

if($file->cacheData('index',null)){
    var_dump($file->cacheData('index',''));
    //echo 'SUCCESS';
}else{
    echo 'ERROR';
}
