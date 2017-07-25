<?php
namespace app\cms\controller;

use app\base\controller\Base;
use app\cms\model\Article;

class Index extends Base{
    public function index()
    {
        return $this->view->fetch();
    }

    public function gettest($p = 1, $keyword = '') {
        $test = new Article();
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
            $test = new Article();
            $state = $test->allowField(true)->save($post_data,$post_data['id']);
            if(false == $state){
                return getMsg("操作失败");
            }
            return getMsg("操作成功");
        }else{
            return $this->view->fetch();
        }
    }

    public function add(){
        if($this->request->isAjax()) {
            $post_data = $this->request->param();
            if (empty($post_data)) {
                return getMsg("数据不能为空");
            }
            $test = new Article();
            $state = $test->allowField(true)->save($post_data);
            if (false == $state) {
                return getMsg("操作失败");
            }
        }
        return getMsg("操作成功");
    }

    public function test(){
        return $this->fetch();
    }

    public function test2(){
        return $this->fetch();
    }
}
