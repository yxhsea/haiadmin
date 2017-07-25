<?php

namespace plugs\uploadImg;
use app\base\controller\Plugs;
use think\Db;

/**
 * 单多图片上传插件
 * @author yxhsea
 */
class uploadImgPlugs extends Plugs{

    public $info =[
        'name'=>'uploadImg',
        'title'=>'单多图片上传',
        'description'=>'单多图片上传插件',
        'webvisit'=>0,
        'status'=>1,
        'author'=>'yxhsea',
        'version'=>'1.0'
    ];

    public function install(){
        //写入钩子
        Db::execute("INSERT INTO `system_hooks` VALUES ('','uploadImg','单多图上传钩子','1',".time().",'')");
        return true;
    }

    public function uninstall(){
        //移除钩子
        Db::execute("DELETE FROM  `system_hooks` WHERE `name` = 'uploadImg'");
        return true;
    }

    //实现的uploadImg钩子方法 {:plugs('uploadImg',['title'=>'图片上传','name'=>'images','value'=>''])}
    public function uploadImg($param){
        if(!empty($param['value'])){
            $images = explode(",",$param['value']);
            foreach ($images as $key => $value){
                $img_arr[$key]['img_id'] = $value;
                $img_arr[$key]['img_url'] = $this->getImgUrl($value);
            }
            $this->assign('img_arr',$img_arr);
        }
        $this->assign('images',$param['value'] == '' ? '' : $param['value']);
        $this->assign('title',$param['title'] == '' ? '' : $param['title']);
        $this->assign('name',$param['name'] == '' ? '' : $param['name']);
        $this->assign('add_img',\think\Request::instance()->root(true).'/plugs/uploadImg/control/images/add_img.png');
        return $this->fetch("index");
    }

    //获取图片地址
    public function getImgUrl($img_id){
        $img_url = Db::name('system_picture')->where(['id'=>$img_id])->value('path');
        if($img_url){
            $img_url = \think\Request::instance()->root(true).'/public'.str_replace('\\','/',$img_url);
        }else{
            $img_url = false;
        }
        return $img_url;
    }
}