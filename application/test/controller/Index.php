<?php
namespace app\test\controller;

use app\base\controller\System;
use app\base\controller\Base;
use app\test\model\Test;
use think\Auth;
use org\Express;
class Index extends Base
{
    public function test(){
        var_dump($this->request->root(true)."/public/static/system/css");exit();
        //$this->getMenu();
        $exp = new Express("ad86e6f484f3581ab522260859cc8475");
        echo "<pre>";
        print_r($exp);
    }

    public function index()
    {
        return $this->view->fetch();
    }

    public function gettest($p = 1, $keyword = '') {
        $test = new Test();
        $p = ($p*10) - 10;
        $list = $test->getTest($keyword, $p);
        $msg['status'] =200;
        $msg['data']['list'] = $list;
        $msg['pages'] = $test->getPage();
        return $msg;
    }
    public function save() {
        if($this->request->isAjax()){
            $post_data = $this->request->param();
            if(empty($post_data)){return getMsg("数据不能为空");}
            $test = new Test();
            $state = $test->allowField(true)->save($post_data,$post_data['id']);
            if(false == $state){
                return getMsg("操作失败");
            }
            return getMsg("操作成功");
        }
    }
}
