<?php
namespace plugs\editor;

use app\base\controller\Plugs;
use think\Db;
/**
 * 编辑器插件
 * @author yxhsea
 */
class editorPlugs extends Plugs{

    public $info =[
        'name'=>'editor',
        'title'=>'编辑器',
        'description'=>'编辑器',
        'webvisit'=>0,
        'status'=>1,
        'author'=>'yxhsea',
        'version'=>'1.0'
    ];

    public function install(){
        //写入钩子
        Db::execute("INSERT INTO `system_hooks` VALUES('','editor','编辑器钩子','1',".time().",'')");
        return true;
    }

    public function uninstall(){
        //移除钩子
        Db::execute("DELETE FROM `system_hooks` WHERE `name` = 'editor'");
        return true;
    }

    //实现的editor钩子方法，使用方法：{:plugs('editor',['name'=>'content','value'=>''])}
    public function editor($param){
        if(!isset($param['name'])){
            $name = 'content';
        }else{
            $name = $param['name'];
        }
        if(!isset($param['value'])){
            $value = '';
        }else{
            $value = $param['value'];
        }
        $this->assign('title',$param['title']);
        $this->assign('name',$name);
        $this->assign('value',$value);
        return $this->fetch('index');
    }
}