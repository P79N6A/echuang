<?php defined('In33hao') or exit('Access Invild!');?>
<div class="page">
	<div class="fixed-bar">
    	<div class="item-title">
      		<div class="subject">
        		<h3>公告管理</h3>
        		<h5>会员系统会员账户信息设置管理</h5>
      		</div>
    	</div>
  	</div>
  	<!-- 操作说明 -->
  	<div class="explanation" id="explanation">
    	<div class="title" id="checkZoom">
    		<i class="fa fa-lightbulb-o"></i>
      		<h4 title="提示相关设置操作时应注意的要点">操作指示</h4>
      		<span id="explanationZoom" title="收起提示"></span>
      	</div>
    	<ul>
      		<li>通过会员管理，你可以进行查看、编辑会员资料等操作</li>
      		<li>你可以根据条件搜索会员，然后选择相应的操作</li>
    	</ul>
  	</div>
  	<div id="flexigrid"></div>
</div>
<script type="text/javascript">
  $(function() {
    $("#flexigrid").flexigrid({
        url: 'index.php?act=notice_setting&op=get_xml',
        colModel : [
            {display: '操作', name: 'operation', width: 160, sortable: false, align: 'center'},
            {display: '标题', name: 'notice_title', width: 160, sortable: false, align: 'center'},
            {display: '类型', name: 'notice_type', width: 160, sortable: false, align: 'center'},
            {display: '添加时间', name: 'notice_addtime', width: 160, sortable: false, align: 'center'},
            {display: '是否显示', name: 'notice_ifshow', width: 160, sortable: false, align: 'center'},
            ],
        buttons : [
            {display: '<i class="fa fa-plus"></i>新增公告', name: 'add', bclass: 'add', title: '新增公告', onpress: fg_operation },
            ],
        sortname: "notice_addtime",
        sortorder: "desc",
        title: '公告文章列表'
    });

});

function fg_operation(name, bDiv) {
    if (name == 'add') {
        window.location.href = 'index.php?act=notice_setting&op=notice_add';
    }
}
function fg_delete(id) {
  if (typeof id == 'number') {
      var id = new Array(id.toString());
  };
  if(confirm('删除后不能恢复，确认删除这公告吗？')){
    id = id.join(',');
  } else {
        return false;
    }
  $.ajax({
        type: "GET",
        dataType: "json",
        url: "index.php?act=notice_setting&op=notice_delete",
        data: "del_id="+id,
        success: function(data){
            if (data.state){
                $("#flexigrid").flexReload();
            } else {
              alert(data.msg);
            }
        }
    });
}
</script>