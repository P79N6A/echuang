<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="page">
    <form method="post" enctype="multipart/form-data" name="form1">
        <input type="hidden" name="MAX_FILE_SIZE" value="1000000">
        <div id="picInput">
            上传图片：<input type="file" name='myfile[]' multiple="multiple">
        </div>
        <input id="addBtn" type="button" onclick="addPic1()" value="继续添加图片"><br/><br/>
        <input type="submit" value="上传文件">
    </form>
</div>
<script>
    function addPic1(){
        var addBtn =  document.getElementById('addBtn');
        var input = document.createElement("input");
        input.type = 'file'; 
        input.name = 'myfile[]';
        var picInut = document.getElementById('picInput');
        picInut.appendChild(input);
        if(picInut.children.length == 3){
            addBtn.disabled = 'disabled';
        }
    }
</script>
