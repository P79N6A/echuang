<?php defined('In33hao') or exit('Access Ivild!');?>
<div class="page">
	<div class="fixed-bar">
      <div class="item-title">
          <div class="subject">
            <h3>服务中心管理</h3>
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
        url: 'index.php?act=service&op=get_xml',
        colModel : [
            {display: '操作', name: 'operation', width : 160, sortable : false, align: 'center'},
            {display: '申请序号', name: 'slg_id', width : 160, sortable : false, align: 'center'},
            {display: '会员手机', name: 'member_mobile', width : 160, sortable : false, align: 'center'},
            {display: '会员姓名', name: 'slg_member_name', width : 160, sortable : false, align: 'center'},
            {display: '申请时间', name: 'slg_addtime', width : 160, sortable : false, align: 'center'},
            {display: '状态', name: 'slg_state', width : 160, sortable : false, align: 'center'},
            {display: '审核时间', name: 'slg_audittime', width : 160, sortable : false, align: 'center'},
        ],
        buttons : [
            {display: '<i class="fa fa-trash"></i>批量删除', name : 'del', bclass : 'del', title : '将选定行数据批量删除', onpress : fg_operation }
        ],
        searchitems: [
            {display: '会员手机', name : 'member_mobile'},
        ],
        sortname: "slg_addtime",
        sortorder: "desc",
        title: '会员申请服务中心列表'
    });

});
function fg_operation(name, bDiv) {
    if (name == 'del') {
        if ($('.trSelected', bDiv).length == 0) {
            showError('请选择要操作的数据项！');
        }
        var itemids = new Array();
        $('.trSelected', bDiv).each(function(i){
            itemids[i] = $(this).attr('data-id');
        });
        fg_del(itemids);
    }
}
function fg_del(ids) {
    var Ids = new Array(ids.toString());
    id = Ids.join(',');
    if(confirm('删除后将不能恢复，确认删除这项吗？')){
        $.getJSON('index.php?act=service&op=del', {'id':id}, function(data){
            if (data.state) {
                showSucc(data.msg)
                $("#flexigrid").flexReload();
            } else {
                showError(data.msg)
            }
        });
    }
}

function fg_xls(ids) {
    id = ids.join(',');
    window.location.href = $("#flexigrid").flexSimpleSearchQueryString()+'&op=export_xls&id='+ id;
}

</script>