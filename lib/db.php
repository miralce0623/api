<?php
/**
 * 链接数据库
 */
$pdo = new PDO('mysql:host=localhost;dbname=mydb','root','',array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
return $pdo;