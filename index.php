<?php
header("Content-type:text/html;charset=utf-8");  

require __DIR__.'/lib/User.php';
require __DIR__.'/lib/Article.php';
$pdo = require __DIR__.'/lib/db.php';
//$pdo->exec("set names utf8");	//解决中文乱码
//$user = new User($pdo);
//print_r($user->login('admin1','admin1'));

$article = new Article($pdo);
print_r($article->getList(4,1,1));