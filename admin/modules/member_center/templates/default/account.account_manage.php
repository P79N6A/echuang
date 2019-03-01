<?php defined('In33hao') or exit('Access Invild!');?>
<div class="page">
	<div class="fixed-bar">
    	<div class="item-title">
      		<div class="subject">
        		<h3>账户管理</h3>
        		<h5>会员系统账户信息设置管理</h5>
      		</div><?php echo $output['top_link']; ?>
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
        url: 'index.php?act=account&op=get_xml',
        colModel : [
            {display: '操作', name : 'operation', width : 100, sortable : false, align: 'center', className: 'handle-s'},
            {display: '会员手机', name: 'member_mobile', width : 160, sortable : false, align: 'center'},
            {display: '真实姓名', name: 'member_name', width : 160, sortable : false, align: 'center'},
            {display: '积分', name: 'integral', width : 120, sortable : false, align: 'center'},
            {display: '预期积分', name: 'estimate_integral', width : 120, sortable : false, align: 'center'},
            {display: '报单次数', name: 'declaration_times', width : 120, sortable : false, align: 'center'},
            {display: '出局次数', name: 'outgo_times', width : 120, sortable : false, align: 'center'},
        ],
        buttons: [
            {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'xls', bclass : 'xls', title : '将选定行数据导出execl文件', onpress : fg_operation },
            {display: "<span style='color:red;'>积分：<?php echo $output['integral_total']; ?></span>", name : 'integral_total', bclass : 'account-bean', title : '积分'},
            {display: "<span style='color:red;'>预期积分：<?php echo $output['estimate_integral_total']; ?></span>", name : 'estimate_integral_total', bclass : 'account-equity', title : '预期积分'},
            ],
        searchitems: [
            {display: '会员手机', name : 'member.member_mobile'},
            {display: '会员姓名', name: 'member.member_name'},
        ],
        sortname: "total_consume",
        sortorder: "desc",
        title: '帐户管理列表'
    });

});

function fg_operation(name, bDiv) {
    if (name == 'add') {
        window.location.href = 'index.php?act=member&op=member_add';
    }
    if (name == 'xls') {
        if ($('.trSelected', bDiv).length == 0) {
            if (!confirm('您确定要下载全部数据吗？')) {
                return false;
            }
        }
        var itemids = new Array();
        $('.trSelected', bDiv).each(function(i){
            itemids[i] = $(this).attr('data-id');
        });
        fg_xls(itemids);
    }
}

function fg_xls(ids) {
    id = ids.join(',');
    window.location.href = $("#flexigrid").flexSimpleSearchQueryString()+'&op=export_xls&id='+ id;
}
</script>