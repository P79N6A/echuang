<?php defined('In33hao') or exit('Access Invild!');?>
<div class="page">
	<div class="fixed-bar">
    	<div class="item-title">
      		<div class="subject">
        		<h3>拨比统计</h3>
        		<h5>拨比统计</h5>
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
      		<li>通过拨比统计，你可以进行查看、导出每日消费与奖金拨比</li>
    	</ul>
  	</div>
  	<div id="flexigrid"></div>
</div>
<script type="text/javascript">
$(function() {
    $("#flexigrid").flexigrid({
        url: 'index.php?act=dial_statistics&op=get_xml',
        colModel : [
            {display: '操作', name: 'operation', width : 160, sortable : false, align: 'center'},
            {display: '消费总额', name: 'bean_consume_amount', width : 160, sortable : false, align: 'center'},
            {display: '奖金总额', name: 'bonus_amount', width : 200, sortable : false, align: 'center'},
            {display: '拨比比率', name: 'dail_ratio', width : 200, sortable : false, align: 'center'},
            {display: '时间', name: 'fs_addtime', width : 230, sortable : false, align: 'center'},
        ],
        buttons: [
            {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'xls', bclass : 'xls', title : '将选定行数据导出execl文件', onpress : fg_operation },
            {display: "<span style='color:red;'>总消费额：<?php echo $output['total_consume_amount']; ?>元</span>", name : 'total_bean', bclass : 'fdial-statistics', title : '金豆总消费额'},
            {display: "<span style='color:red;'>总奖金总额：<?php echo $output['total_bonus_amount']; ?>元</span>", name : 'total_equity', bclass : 'fdial-statistics-item', title : '总奖金总额'},
            {display: "<span style='color:red;'>总拨比率：<?php echo $output['total_dail_ratio']; ?>%</span>", name : 'total_consume', bclass : 'fdial-statistics-item', title : '总拨比率'},
            ],
        // searchitems: [
        //     {display: '会员手机', name : 'member_mobile'},
        //     {display: '会员姓名', name: 'member_name'},
        // ],
        sortname: "fs_updatetime",
        sortorder: "desc",
        title: '拨比统计列表'
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