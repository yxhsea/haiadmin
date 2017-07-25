<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 检测是否是新安装
if(file_exists("./public/install") && !file_exists("./public/install/install.lock")){
    // 组装安装url
    $url=$_SERVER['HTTP_HOST'].'/public/install/index.php';
    // 使用http://域名方式访问；避免./Public/install 路径方式的兼容性和其他出错问题
    header("Location:http://$url");
    die;
}

// [ 应用入口文件 ]

//定义开发者模式,项目上线后定义为false
defined('IS_DEV') or define('IS_DEV', true);

// 定义应用目录
define('APP_PATH', __DIR__ . '/app/');
// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';