<?php
use think\Db;

function getMsg($msg = "未知错误", $url = "", $render = true, $status = 200) {
    $msg = [
        'status'    =>  $status,
        'render'    =>  $render,
        'msg'       =>  $msg,
        'url'       =>  $url,
    ];
    return $msg;
}

function getImgUrl($id = 1){
    $img = Db::table('system_picture')->where(['id'=>$id])->field('path')->find();
    return $img['path'];
}

/**
 * 列表渲染,返回json数据
 * @param $list 列表数据(分页的数据)
 * @param int $page 总页数
 * @param int $status 返回状态码,200为成功,其它为失败
 */
function getMsgJson($list,$page = 2,$status = 200){
    $msg['status'] = $status;
    $msg['data']['list'] = $list;
    $msg['pages'] = $page;
    echo header("content-type:text/html;charset=utf-8");
    echo json_encode($msg);
}

/**
 * 菜单树、或分类树
 * @param $arr 数组
 * @param int $pid 父级id
 * @param int $level 缩进
 * @return array
 */
function getTree($arr,$pid=0,$level=0){
    static $array = array();
    foreach ($arr as $key => $value){
        if($pid == $value['pid']){
            if($level == 0){
                $value['level'] = '';
            }else{
                $value['level'] = $level == 1 ? str_repeat('|&nbsp;----&nbsp;',$level) : '|'.str_repeat('&nbsp;----&nbsp;',$level);
            }
            $array[] = $value;
            getTree($arr,$value['id'],$level+1);
        }
    }
    return $array;
}

/**
 * 获取数据库中的配置列表
 * @return array 配置数组
 */
function config_lists(){
    $data   = db('system_config')->where(['status'=>1])->field('type,name,value')->select();
    $config = [];
    if($data && is_array($data)){
        foreach ($data as $value) {
            $config[$value['name']] = parse($value['type'], $value['value']);
        }
    }
    return $config;
}

/**
 * 根据配置类型解析配置
 * @param  integer $type  配置类型
 * @param  string  $value 配置值
 */
function parse($type, $value){
    if(3 == $type){
        $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
        if(strpos($value,':')){
            $value  = [];
            foreach ($array as $val) {
                list($k, $v) = explode(':', $val);
                $value[]   = ['key'=>$k,'value'=>$v];
            }
        }else{
            $value =  $array;
        }
    }
    return $value;
}

/**
 * auth密码加密方式
 * @param $password
 * @return string
 */
function auth_password($password){
    return md5(md5($password).config('auth_key'));
}

function int_to_string(&$data,$map=['status'=>[1=>'正常',-1=>'删除',0=>'禁用',2=>'未审核',3=>'草稿']]) {
    if($data === false || $data === null ){
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row){
        foreach ($map as $col=>$pair){
            if(isset($row[$col]) && isset($pair[$row[$col]])){
                $data[$key][$col.'_text'] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}

/**
 * 对查询结果集进行排序
 * @access Public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list,$field, $sortby='asc') {
    if(is_array($list)){
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc':// 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ( $refer as $key=> $val)
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}

/**
 * 处理插件钩子
 * @param string $plugs   钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function plugs($plugs,$params=[]){
    $result = \think\Hook::listen($plugs,$params);
    if(isset($result[0])){
        return $result[0];
    }else{
        return false;
    }
}

//插件控制器url生成
//$url传参格式 必须为 插件名/插件控制器/插件方法
//$getParam 为get的数组
function plugUrl($url,$getParam = [],$type = 1){
    if(!$url) return false;

    $arr = explode('/', $url);
    foreach ($arr as $key => $value) {
        switch ($key) {
            case '0':
                //插件名称
                $param['plugname'] = $value;
                break;

            case '1':
                //插件控制器
                $param['plugaction'] = $value;
                break;
            case '2':
                //插件方法
                $param['plugfun'] = $value;
                break;

        }
    }

    $param = array_merge($param,$getParam);

    //如果$type = 1那么为后台控制器的URL生成
    if($type == 1){
        $urls = 'system/Plugs/open';
    }else{
        $urls = 'index/Plugs/open';
    }
    return url($urls,$param);
}

/**
 * 构建层级（树状）数组
 * @param array  $array          要进行处理的一维数组，经过该函数处理后，该数组自动转为树状数组
 * @param string $pid_name       父级ID的字段名
 * @param string $child_key_name 子元素键名
 * @return array|bool
 */
function array2tree(&$array, $pid_name = 'pid', $child_key_name = 'sub')
{
    $counter = array_children_count($array, $pid_name);
    if (!isset($counter[0]) || $counter[0] == 0) {
        return $array;
    }
    $tree = [];
    while (isset($counter[0]) && $counter[0] > 0) {
        $temp = array_shift($array);
        if (isset($counter[$temp['id']]) && $counter[$temp['id']] > 0) {
            array_push($array, $temp);
        } else {
            if ($temp[$pid_name] == 0) {
                $tree[] = $temp;
            } else {
                $array = array_child_append($array, $temp[$pid_name], $temp, $child_key_name);
            }
        }
        $counter = array_children_count($array, $pid_name);
    }

    return $tree;
}

/**
 * 子元素计数器
 * @param array $array
 * @param int   $pid
 * @return array
 */
function array_children_count($array, $pid)
{
    $counter = [];
    foreach ($array as $item) {
        $count = isset($counter[$item[$pid]]) ? $counter[$item[$pid]] : 0;
        $count++;
        $counter[$item[$pid]] = $count;
    }

    return $counter;
}

/**
 * 把元素插入到对应的父元素$child_key_name字段
 * @param        $parent
 * @param        $pid
 * @param        $child
 * @param string $child_key_name 子元素键名
 * @return mixed
 */
function array_child_append($parent, $pid, $child, $child_key_name)
{
    foreach ($parent as &$item) {
        if ($item['id'] == $pid) {
            if (!isset($item[$child_key_name]))
                $item[$child_key_name] = [];
            $item[$child_key_name][] = $child;
        }
    }

    return $parent;
}

//输出函数
function p($data){
    echo "<pre>";
    print_r($data);
    exit();
}

// 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}