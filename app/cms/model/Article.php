<?php
/*
* 
* Created by PhpStorm.
* Author: 初心 [jialin507@foxmail.com]
* Date: 2017/6/20
*/
namespace app\cms\model;

use think\model;

class Article extends model{
    // 关闭自动写入update_time字段
    protected $updateTime = false;

    //设置当前模型对应的完整数据表名称
    protected $table = 'cms_article';

    public function getTest($keyword = '', $start = 0, $limit = 10){
        $data = $this->where(['title'=>['like','%'.$keyword.'%']])->limit($start, $limit)->order('orders ASC')->select();

        return $data;
    }

    public function getPage($limit = 10){
        $page = $this->count();
        $page = floor($page / $limit);
        return $page;
    }
}