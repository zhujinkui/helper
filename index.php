<?php
// 生成全局唯一ID的PHP实现类
// +----------------------------------------------------------------------
// | PHP version 5.3+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.myzy.com.cn, All rights reserved.
// +----------------------------------------------------------------------
// | Author: 阶级娃儿 <262877348@qq.com> 群：304104682
// +----------------------------------------------------------------------
header("Content-Type: Text/Html;Charset=UTF-8");
require "./vendor/autoload.php";

$idWorker = new \think\SnowflakeIdWorker(0, 0);

for ($i = 0; $i < 100; $i++) {
    $id = $idWorker->nextId();
    echo $id . PHP_EOL;
    echo '<br/>';
}