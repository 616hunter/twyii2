<?php
//引入数据库操作类
require 'DB.class.php';
//实例化对象
$db = DB::getInstance(['password'=>'root','dbname'=>'yii2shop']);
//拼凑sql语句
$pid = isset($_GET['pid'])?$_GET['pid']-0:0;
$sql = "SELECT * FROM locations WHERE parent_id=$pid";
//执行sql语句
$rows = $db->fetchAll($sql);
//返回结果
echo json_encode($rows);
