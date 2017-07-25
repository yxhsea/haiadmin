<?php
namespace app\base\controller;

use think\Auth;
use think\Loader;
use think\Cache;
use think\Controller;
use think\Db;
use think\Session;
use think\Config;

/**
 * 后台公用基础控制器
 * Class AdminBase
 * @package app\base\controller
 */
class Base extends Controller{
    protected function _initialize(){
        $this->_init();

        //动态设置配置
        $config = Cache::get('cache_config');
        if(!$config){
            $config = config_lists();
            Cache::set('cache_config',$config);
        }
        Config::set($config);

        //Session::set('admin_user_id',1);
        parent::_initialize();

        $this->checkAuth();

        //$this->getMenu();

        // 输出当前请求控制器（配合后台侧边菜单选中状态）
        //$this->assign('controller', Loader::parseName($this->request->controller()));
    }

    //初始化方法
    protected function _init(){
        defined('TCMS_VERSION') or define('TCMS_VERSION','2.1.170111'); // Tplus版本
        defined('PLUGS_PATH_NAME') or define('PLUGS_PATH_NAME','plugs'); // 插件目录名称
        defined('PLUGS_PATH') or define('PLUGS_PATH', ROOT_PATH.PLUGS_PATH_NAME); // 插件目录

        defined('MODULE_PATH_NAME') or define('MODULE_PATH_NAME','application'); // 模块目录名称
        defined('MODULE_PATH') or define('MODULE_PATH', ROOT_PATH.MODULE_PATH_NAME); // 插件目录

        $rootUrl = $this->request->root(true); //ROOT域名

        //模板资源变量分配
        /*foreach (config('TMPL_PARSE_STRING') as $key => $value) {
            $this->view->assign('_'.$key,$rootUrl.$value);
        }*/
    }

    /**
     * 权限检查
     * @return bool
     */
    protected function checkAuth(){

        if (!Session::has('admin_user_id')) {
            $this->redirect('system/login/index');
        }

        $module     = $this->request->module();
        $controller = $this->request->controller();
        $action     = $this->request->action();

        $changeStatus = $module . '/' . $controller . '/changestatus';
        $changeSort = $module . '/' . $controller . '/changesort';

        // 排除权限
        $not_check = ['system/Index/index','base/Base/getmenu',$changeStatus,$changeSort];

        if (!in_array($module . '/' . $controller . '/' . $action, $not_check)) {
            $auth     = new Auth();
            $admin_user_id = Session::get('admin_user_id');
            if (!$auth->check($module . '/' . $controller . '/' . $action, $admin_user_id) && $admin_user_id != 1) {
                $this->error('权限不足,请联系管理员升级权限!');
            }
        }
    }

    /**
     * 获取侧边栏菜单
     */
    public function getMenu(){
        $menu     = [];
        $admin_user_id = Session::get('admin_user_id');
        $auth     = new Auth();
        $auth_rule_list = Db::name('system_auth_rule')->where(['status'=>1])->order(['sort' => 'DESC'])->select();
        foreach ($auth_rule_list as &$value) {
            if ($auth->check($value['name'], $admin_user_id) || $admin_user_id == 1) {
                $value['name'] = url($value['name']);
                $menu[] = $value;
            }
        }

        $menu = !empty($menu) ? array2tree($menu) : [];

        $msg['data']['list'] = $menu;
        $msg['status']=200;
        echo header("content-type:text/html; charset=utf-8");
        echo json_encode($msg);
    }

    /**
     * 修改状态
     * @param $model
     * @param $id
     * @param $status
     * @return array|string
     */
    public function changeStatus($model, $id, $status){
        $res = Db::table(strtolower($model))->where(['id'=>$id])->update(['status'=>$status == 'on']);
        if(!$res){
            return getMsg("修改失败");
        }
        return getMsg("修改成功");
    }

    /**
     * 修改排序
     * @param $model
     * @param $id
     * @param $sort
     * @return array|string
     */
    public function changeSort($model, $id, $sort){
        $res = Db::table(strtolower($model))->where(['id'=>$id])->update(['sort'=>$sort]);
        if(!$res){
            return getMsg("修改失败");
        }
        return getMsg("修改成功","reload");
    }

    /**
     * 删除操作
     * @param $model
     * @param $id
     * @return array|string
     */
    public function delete($model, $id) {
        if(empty($id)){return getMsg("删除失败");}
        $res = Db::table(strtolower($model))->where(['id'=>$id])->delete();
        if(!$res){
            return getMsg("删除失败");
        }
        return getMsg("删除成功","reload");
    }
}