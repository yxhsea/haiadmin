##### 单多图上传插件使用说明
> 使用示例,在视图模板中调用{:plugs('uploadImg',['title'=>'图片上传','name'=>'images','value'=>'1,2,3'])}
```
<form id="form1" class="layui-form layui-form-pane" data-render="true" action="add">
    <div class="layui-form-item">
        <label class="layui-form-label">文章标题</label>
        <div class="layui-input-block">
            <input type="text" name="title" required jq-verify="" jq-error="文章标题" placeholder="请输入标题" autocomplete="off" class="layui-input ">
        </div>
    </div>
    {:plugs('uploadImg',['title'=>'图片上传','name'=>'images','value'=>'1,2,3'])}    
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" jq-submit lay-filter="submit">立即提交</button>
            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
    </div>
</form>
```