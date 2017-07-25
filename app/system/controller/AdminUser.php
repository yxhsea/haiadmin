<?php
/**
 * author      : Yxhsea.
 * email       : Yxhsea@foxmail.com
 * createTime  : 2017/7/5 21:08
 * description : 管理员管理
 */
namespace app\system\controller;

use app\base\controller\Base;
use app\system\model\AdminUser as AdminUserModel;
use app\system\model\AuthGroup as AuthGroupModel;
use app\system\model\AuthGroupAccess as AuthGroupAccessModel;
class AdminUser extends Base{
    protected $admin_user_model;
    protected $auth_group_model;
    protected $auth_group_access_model;

    protected function _initialize()
    {
        parent::_initialize();
        $this->admin_user_model = new AdminUserModel();
        $this->auth_group_model = new AuthGroupModel();
        $this->auth_group_access_model = new AuthGroupAccessModel();
    }

    /**
     * 加载管理员列表数据
     * @return mixed|void
     */
    public function adminUserList(){
        if($this->request->isAjax()){
            $admin_user_list = $this->admin_user_model->order('sort','desc')->select();
            foreach ($admin_user_list as &$value){
                $value['last_login_time'] = date("Y-m-d H:i:s",$value['last_login_time']);
                $value['group_id'] = $this->auth_group_access_model->where(['uid'=>$value['id']])->value('group_id');
                $value['group_name'] = $this->auth_group_model->where(['id'=>$value['group_id']])->value('title');
            }
            return getMsgJson($admin_user_list);
        }
        $auth_group_list = $this->auth_group_model->select();
        $this->assign("auth_group_list",$auth_group_list);
        return $this->fetch();
    }

    /**
     * 新增、修改管理员
     * @return array|string
     */
    public function save(){
        if($this->request->isAjax()){
            $post_data = $this->request->param();
            if(empty($post_data)){
                return getMsg("数据不能为空");
            }
            if($post_data['id'] == 0){
                $data = [
                    'username'        => $post_data['username'],
                    'password'        => $post_data['password'],
                    'status'          => $post_data['status'],
                    'sort'            => $post_data['sort'],
                    'last_login_time' => time(),
                    'last_login_ip'   => '0.0.0.0',
                    'create_time'     => time()
                ];
                $uid = $this->admin_user_model->insertGetId($data);
                $res = $this->auth_group_access_model->insert(['uid'=>$uid,'group_id'=>$post_data['group_id']]);
            }else{
                $data = [
                    'username'    => $post_data['username'],
                    'password'    => $post_data['password'],
                    'status'      => $post_data['status'],
                    'sort'        => $post_data['sort'],
                ];
                if($data['password'] == ''){
                    unset($data['password']);
                }
                $res = $this->admin_user_model->where(['id'=>$post_data['id']])->update($data);
                $this->auth_group_access_model->where(['uid'=>$post_data['id']])->update(['group_id'=>$post_data['group_id']]);
            }
            if($res){
                return getMsg("操作成功","reload");
            }
            return getMsg("操作失败","reload");
        }
    }
}