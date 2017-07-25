<?php
//配置文件
return [
// 视图输出字符串内容替换
    'view_replace_str'       => [
        '__ROOT__'      =>  request()->root() ,
        '__STATIC__'    =>  '/public/static/system',
        '__MODULES__'   =>  '/public/static/system/modules',
        '__CSS__'       =>  '/public/static/system/css',
        '__IMG__'       =>  '/public/static/system/images',
        '__JS__'        =>  '/public/static/system/js',
    ],
];