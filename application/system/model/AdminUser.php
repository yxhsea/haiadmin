<?php
/**
 * author      : Yxhsea.
 * email       : Yxhsea@foxmail.com
 * createTime  : 2017/7/5 21:21
 * description : 管理员模型
 */
namespace app\system\model;

use think\Model;
class AdminUser extends Model{
    protected $table = "system_admin_user";
    protected $updateTime = false;

    public function checkLogin($username,$password){
        $id = $this->where(['username'=>$username,'password'=>$password])->value('id');
        return $id ? $id : false;
    }
}