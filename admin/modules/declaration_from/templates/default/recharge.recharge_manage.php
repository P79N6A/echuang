<?php defined('In33hao') or exit('Access Invild!');?>
<div class="page">
	<div class="fixed-bar">
      <div class="item-title">
          <div class="subject">
            <h3>充值管理</h3>
            <h5>会员系统会员充值信息设置管理</h5>
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
        url: 'index.php?act=recharge&op=get_xml',
        colModel : [
            {display: '操作', name: 'operation', width : 100, sortable : false, align: 'center', className: 'handle-s'},
            {display: '充值单号', name: 'rl_sn', width : 110, sortable : false, align: 'center'},
            {display: '会员手机', name: 'member_mobile', width : 110, sortable : false, align: 'center'},
            {display: '真实姓名', name: 'rl_member_name', width : 110, sortable : false, align: 'center'},
            {display: '支付方式', name: 'rl_recharge_type', width : 110, sortable : false, align: 'center'},
            {display: '充值账户', name: 'rl_account_type', width : 110, sortable : false, align: 'center'},
            {display: '充值金额', name: 'rl_amount', width : 110, sortable : false, align: 'center'},
            {display: '申请时间', name: 'rl_addtime', width : 110, sortable : false, align: 'center'},
            {display: '支付时间', name: 'rl_paytime', width : 110, sortable : false, align: 'center'},
            {display: '状态', name: 'rl_state', width : 50, sortable : false, align: 'center'},
        ],
        buttons : [
            {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'xls', bclass : 'xls', title : '将选定行数据导出execl文件', onpress : fg_operation }
            ],
        searchitems: [
            {display: '会员手机', name : 'member_mobile'},
            {display: '会员姓名', name: 'rl_member_name'},
            {display: '充值单号', name: 'rl_sn'},
        ],
        sortname: "total_consume",
        sortorder: "desc",
        title: '帐户管理列表'
    });

});

function fg_operation(name, bDiv) {

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