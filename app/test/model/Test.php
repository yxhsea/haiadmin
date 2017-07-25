<?php
/*
* 
* Created by PhpStorm.
* Author: 初心 [jialin507@foxmail.com]
* Date: 2017/6/19
*/
namespace app\test\model;

use think\model;
class Test extends Model{
    // 关闭自动写入update_time字段
    protected $updateTime = false;

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
