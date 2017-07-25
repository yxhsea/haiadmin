<?php
/**
 * author      : Yxhsea.
 * email       : Yxhsea@foxmail.com
 * createTime  : 2017/7/2 21:52
 * description : 权限组管理
 */
namespace app\system\controller;

use app\base\controller\Base;
use app\system\model\AuthGroup as AuthGroupModel;
use app\system\model\AuthRule as AuthRuleModel;
use think\Db;
class AuthGroup extends Base{
    protected $auth_group_model;
    protected $auth_rule_model;

    protected function _initialize(){
        parent::_initialize();
        $this->auth_group_model = new AuthGroupModel();
        $this->auth_rule_model  = new AuthRuleModel();
    }

    /**
     * 权限组列表
     * @return mixed
     */
    public function authGroupList(){
        if($this->request->isAjax()){
            $auth_group_list =  $this->auth_group_model->select()->toArray();
            //处理rules,适应前端显示
            foreach ($auth_group_list as $key => &$value){
               foreach (explode(',',$value['rules']) as $val){
                   $arr[$val] = $val;
               }
               $value = $value + $arr;
            }
            return getMsgJson($auth_group_list);
        }
        $this->assign('auth_rule_list',$this->getAuthRule());
        return $this->fetch();
    }

    public function getAuthRule($pid = 0) {
        $where['pid'] = $pid;
        $result = $this->auth_rule_model->where($where)->select();
        foreach ($result as $key=>$value){
            $result[$key]['sub'] = $this->getAuthRule($value['id']);
        }
        return $result;
    }

    /**
     * 新增、修改权限组
     * @return array|string
     */
    public function save_auth(){
        if($this->request->isAjax()){
            $post_data = $this->request->param();
            if(empty($post_data)){
                return getMsg("数据不能为空");
            }
            $rules = '';
            //拼接rules权限节点
            while(list($key,$value) = each($post_data)){
                if(is_numeric($value)&&is_numeric($key)){
                    $rules .= $value.",";
                    unset($post_data[$key]);
                }
            }
            $post_data['rules'] = rtrim($rules,',');
            $res = $this->auth_group_model->allowField(true)->save($post_data,$post_data['id']);
            if($res){
                return getMsg("操作成功","reload");
            }
            return getMsg("操作失败");
        }
    }
}