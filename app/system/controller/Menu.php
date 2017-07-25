<?php
namespace app\system\controller;
/*
* Created by PhpStorm.
* Author: 初心 [jialin507@foxmail.com]
* Date: 2017/3/24
*/
use app\base\controller\Base;
use app\system\model\Menu as MenuModel;
use think\Db;
class Menu extends Base{
    /**
     * 渲染菜单列表页面(select数据)
     * @return mixed
     */
    public function menuList(){
        //ajax 获取菜单列表
        if($this->request->isAjax()){
            $menu = new MenuModel();
            $list = $menu->order(['sort'=>'DESC','id'=>'ASC'])->select();
            return getMsgJson(getTree($list));
        }
        $menu = new MenuModel();
        $list = $menu->select();
        $this->assign('menu',getTree($list));
        return $this->fetch();
    }

    /**
     * 新增，修改
     * @return array|string
     */
    public function save() {
        if($this->request->isAjax()){
            $post_data = $this->request->param();
            if(empty($post_data)){return getMsg("数据不能为空");}
            $menu = new MenuModel();
            $res = $menu->allowField(true)->save($post_data,$post_data['id']);
            if(!$res){
                return getMsg("操作失败");
            }
            return getMsg("操作成功","reload");
        }
    }

    /**
     * 重写父类删除操作
     * @param $model
     * @param $id
     * @return array|string
     */
    public function Delete($model = '', $id) {
        if(empty($id)){return getMsg("删除失败");}
        $menu = new MenuModel();
        $result = $menu->getSubMenu($id);
        if(!empty($result)){
            return getMsg("删除失败,存在下级菜单");
        }
        $res = $menu->where(['id'=>$id])->delete();
        if(!$res){
            return getMsg("删除失败");
        }
        return getMsg("删除成功","reload");
    }

    /**
     * 批量删除操作
     * @param string $model
     * @param $checkbox
     * @return array|string
     */
    public function allDelete($model = '', $checkbox) {
        $ids = explode(',',$checkbox);
        if (empty($ids)){return getMsg("删除失败");}
        $menu = new MenuModel();
        for ($i = 0; $i< count($ids); $i++){
            $result = $menu->getSubMenu($ids[$i]);
            if(!empty($result)){
                return getMsg("删除中止, id：".$ids[$i]."下级存在菜单");
            }
            $menu->where(['id'=>$ids[$i]])->delete();
        }
        return getMsg("删除成功","reload");

    }

    /**
     * 菜单图标列表
     * @return string
     */
    public function demo_unicode() {
        return $this->view->fetch();
    }
}
