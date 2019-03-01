<?php defined('In33hao') or exit('Access Invild!');?>
<div class="page">
	<div class="fixed-bar">
      <div class="item-title">
          <div class="subject">
            <h3>提现管理</h3>
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
    <div class="ncap-search-ban-s" id="searchBarOpen"><i class="fa fa-search-plus"></i>高级搜索</div>
    <div class="ncap-search-bar">
        <div class="handle-btn" id="searchBarClose"><i class="fa fa-search-minus"></i>收起边栏</div>
        <div class="title">
            <h3>高级搜索</h3>
        </div>
        <form method="get" name="formSearch" id="formSearch">
            <div id="searchCon" class="content">
                <div class="layout-box">
                    <dl>
                        <dt>关键字搜索</dt>
                        <dd>
                            <label>
                                <select class="s-select" name="keyword_type">
                                    <option selected="selected" value="">-请选择-</option>
                                    <option value="member_mobile">会员手机</option>
                                    <option value="member_name">会员姓名</option>
                                </select>
                            </label>
                            <label>
                                <input type="text" value="" placeholder="请输入关键字" name="keyword" class="s-input-txt">
                            </label>
                        </dd>
                    </dl>
                    <dl>
                        <dt>支付状态</dt>
                        <dd>
                            <label>
                                <select name="pay_state" class="s-select">
                                    <option value="">-请选择-</option>
                                    <option value="0">待支付</option>
                                    <option value="1">已支付</option>
                                    <option value="2">已拒绝</option>
                                </select>
                            </label>
                        </dd>
                    </dl>
                    <dl>
                        <dt>提现时间</dt>
                        <dd>
                            <label>
                                <input readonly id="query_start_date" placeholder="请选择起始时间" name=query_start_date value="" type="text" class="s-input-txt" />
                            </label>
                            <label>
                                <input readonly id="query_end_date" placeholder="请选择结束时间" name="query_end_date" value="" type="text" class="s-input-txt" />
                            </label>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="bottom"><a href="javascript:void(0);" id="ncsubmit" class="ncap-btn ncap-btn-green mr5">提交查询</a><a href="javascript:void(0);" id="ncreset" class="ncap-btn ncap-btn-orange" title="撤销查询结果，还原列表项所有内容"><i class="fa fa-retweet"></i><?php echo $lang['nc_cancel_search']; ?></a></div>
        </form>
    </div>
</div>
<script type="text/javascript">
$(function() {
    $("#flexigrid").flexigrid({
        url: 'index.php?act=withdraw&op=get_xml',
        colModel : [
            {display: '操作', name: 'operation', width : 160, sortable : false, align: 'center'},
            {display: '会员手机', name: 'member_mobile', width : 160, sortable : false, align: 'center'},
            {display: '会员姓名', name: 'member_name', width : 160, sortable : false, align: 'center'},
            {display: '提现单号', name: 'wl_sn', width : 160, sortable : false, align: 'center'},
            {display: '开户行', name: 'wl_account_bank_name', width : 160, sortable : false, align: 'center'},
            {display: '开户支行', name: 'wl_account_branch', width : 160, sortable : false, align: 'center'},
            {display: '开户姓名', name: 'wl_account_name', width : 160, sortable : false, align: 'center'},
            {display: '银行账号', name: 'wl_account_num', width : 160, sortable : false, align: 'center'},
            {display: '提现金额', name: 'wl_amount', width : 160, sortable : false, align: 'center'},
            {display: '手续费', name: 'wl_fee', width : 160, sortable : false, align: 'center'},
            {display: '到账金额', name: 'wl_actual_amount', width : 160, sortable : false, align: 'center'},
            {display: '提现时间', name: 'wl_addtime', width : 160, sortable : false, align: 'center'},
            {display: '提现状态', name: 'wl_state', width : 160, sortable : false, align: 'center'},
            {display: '支付时间', name: 'wl_paytime', width : 160, sortable : false, align: 'center'},
            // {display: '账户类型', name: 'wl_type', width : 160, sortable : false, align: 'center'},
        ],
        buttons : [
            {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'xls', bclass : 'xls', title : '将选定行数据导出execl文件', onpress : fg_operation },
            {display: '<i class="fa fa-file-excel-o"></i>批量支付提现', name : 'pay', bclass : 'del', title : '批量更改支付提现状态', onpress : fg_payoperation },
            {display: '<i class="fa fa-file-excel-o"></i>批量拒绝提现', name : 'defuse', bclass : 'del', title : '批量更改拒绝提现状态', onpress : fg_defuseoperation },
            {display: '<i class="fa fa-file-excel-o"></i>导出所有已处理', name : 'handled', bclass : 'del', title : '导出所有的已处理的提现订单', onpress : fg_handledoperation },
            {display: '<i class="fa fa-file-excel-o"></i>导出所有未处理', name : 'handled', bclass : 'del', title : '导出所有的未处理的提现订单', onpress : fg_unhandledoperation },
            {display: '<i class="fa fa-file-excel-o"></i>导出所有已拒绝', name : 'refused', bclass : 'del', title : '导出所有的已拒绝的提现订单', onpress : fg_refuseddoperation }
        ],
        searchitems: [
            {display: '会员手机', name : 'member_mobile'},
            {display: '会员姓名', name: 'member_name'},
            {display: '提现单号', name: 'wl_sn'},
        ],
        sortname: "total_consume",
        sortorder: "desc",
        title: '提现管理列表'
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

function fg_handledoperation() {
    window.location.href = $("#flexigrid").flexSimpleSearchQueryString()+'&op=export_handle_exls&type=handle';
}
function fg_unhandledoperation() {
    window.location.href = $("#flexigrid").flexSimpleSearchQueryString()+'&op=export_handle_exls&type=unhandle';
}
function fg_refuseddoperation() {
    window.location.href = $("#flexigrid").flexSimpleSearchQueryString()+'&op=export_handle_exls&type=refused';
}

// 批量更改支付提现
function fg_payoperation(name, bDiv) {
    if (name == 'pay') {
        if ($('.trSelected', bDiv).length == 0) {
            showError('请选择要操作的数据项！');
        }
        var itemids = new Array();
        $('.trSelected', bDiv).each(function(i){
            itemids[i] = $(this).attr('data-id');
        });
        fg_pay(itemids);
    }
}

function fg_pay(ids) {
    var Ids = new Array(ids.toString());
    id = Ids.join(',');
    if(confirm('确认修改状态吗？')){
        $.getJSON('index.php?act=withdraw&op=withdraw_payment', {'id':id}, function(data){
            if (data.state) {
                showSucc(data.msg)
                $("#flexigrid").flexReload();
            } else {
                showError(data.msg)
            }
        });
    }
}
//批量更改拒绝提现
function fg_defuseoperation(name, bDiv) {
    if (name == 'defuse') {
        if ($('.trSelected', bDiv).length == 0) {
            showError('请选择要操作的数据项！');
        }
        var itemids = new Array();
        $('.trSelected', bDiv).each(function(i){
            itemids[i] = $(this).attr('data-id');
        });
        fg_defuse(itemids);
    }
}
function fg_defuse(ids) {
    var Ids = new Array(ids.toString());
    id = Ids.join(',');
    if(confirm('确认修改状态吗？')){
        $.getJSON('index.php?act=withdraw&op=withdraw_refuse', {'id':id}, function(data){
            if (data.state) {
                showSucc(data.msg)
                $("#flexigrid").flexReload();
            } else {
                showError(data.msg)
            }
        });
    }
}

// 高级搜索提交
$('#ncsubmit').click(function() {
    $("#flexigrid").flexOptions({ url: 'index.php?act=withdraw&op=get_xml&' + $("#formSearch").serialize(), query: '', qtype: '' }).flexReload();
});

// 高级搜索重置
$('#ncreset').click(function() {
    $("#flexigrid").flexOptions({ url: 'index.php?act=withdraw&op=get_xml' }).flexReload();
    $("#formSearch")[0].reset();
});
$('#query_start_date').datepicker();
$('#query_end_date').datepicker();
</script>