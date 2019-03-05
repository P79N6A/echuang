<?php defined('In33hao') or exit('Access Invild!');?>
<div class="page">
	<div class="fixed-bar">
      <div class="item-title">
          <div class="subject">
            <h3>积分交易管理</h3>
            <h5>会员系统积分交易管理</h5>
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
          <li>通过积分挂卖交易管理，你可以进行查看所有会员的积分挂卖交易管理</li>
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
            {display: '操作', name: 'operation', width : 150, sortable : false, align: 'center'},
            {display: '单号', name: 'sell_sn', width : 150, sortable : false, align: 'center'},
            {display: '会员手机', name: 'member_mobile', width : 100, sortable : false, align: 'center'},
            {display: '真实姓名', name: 'member_name', width : 100, sortable : false, align: 'center'},
            {display: '状态', name: 'state', width : 80, sortable : false, align: 'center'},
            {display: '挂卖积分', name: 'sell_integral', width : 80, sortable : false, align: 'center'},
            {display: '实际到市场积分', name: 'actual_integral', width : 80, sortable : false, align: 'center'},
            {display: '挂卖时间', name: 'add_time', width : 120, sortable : false, align: 'center'},
            {display: '回购时间', name: 'buy_time', width : 120, sortable : false, align: 'center'},
            {display: '拒绝时间', name: 'refuse_time', width : 120, sortable : false, align: 'center'},
        ],
        buttons : [
            {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'xls', bclass : 'xls', title : '将选定行数据导出execl文件', onpress : fg_operation }
            ],
        searchitems: [
            {display: '会员手机', name : 'member_mobile'},
            {display: '会员姓名', name: 'rl_member_name'},
            {display: '单号', name: 'sell_sn'},
        ],
        sortname: "total_consume",
        sortorder: "desc",
        title: '积分交易管理列表'
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