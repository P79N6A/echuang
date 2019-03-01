<div class="page">
	<div class="fixed-bar">
      <div class="item-title">
          <div class="subject">
            <h3>报单列表</h3>
            <h5></h5>
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
          <li>通过报单列表，你可以进行查看、编辑报单信息等操作</li>
          <li>你可以根据条件搜索报单，然后选择相应的操作</li>
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
                        <dt>下单时间</dt>
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
        url: 'index.php?act=declaration_list&op=get_xml',
        colModel : [
            {display: '单号', name: 'order_sn', width : 140, sortable : false, align: 'center'},
            {display: '会员ID', name: 'member_id', width : 80, sortable : false, align: 'center'},
            {display: '会员姓名', name: 'member_name', width : 80, sortable : false, align: 'center'},
            {display: '填写推荐人ID', name: 'inviter_id', width : 80, sortable : false, align: 'center'},
            {display: '实际推荐人ID', name: 'invite_one', width : 80, sortable : false, align: 'center'},
            {display: '下单时间', name: 'add_time', width : 120, sortable : false, align: 'center'},
            {display: '开始时间', name: 'integral_start_time', width : 120, sortable : false, align: 'center'},
            {display: '结束时间', name: 'integral_end_time', width : 120, sortable : false, align: 'center'},
            {display: '状态', name: 'state', width : 80, sortable : false, align: 'center'},
            {display: '操作', name: 'operation', width : 200, sortable : false, align: 'center'},
        ],
        buttons : [
            // {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'xls', bclass : 'xls', title : '将选定行数据导出execl文件', onpress : fg_operation }
            ],
        searchitems: [
            {display: '单号', name : 'order_sn'},
            {display: '会员ID', name: 'member_id'},
            {display: '填写推荐人ID', name: 'inviter_id'},
            {display: '实际推荐人ID', name: 'invite_one'},
        ],
        sortname: "addtime",
        sortorder: "desc",
        title: '报单列表'
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

// 高级搜索提交
$('#ncsubmit').click(function() {
    $("#flexigrid").flexOptions({ url: 'index.php?act=declaration_list&op=get_xml&' + $("#formSearch").serialize(), query: '', qtype: '' }).flexReload();
});

// 高级搜索重置
$('#ncreset').click(function() {
    $("#flexigrid").flexOptions({ url: 'index.php?act=declaration_list&op=get_xml' }).flexReload();
    $("#formSearch")[0].reset();
});
$('#query_start_date').datepicker();
$('#query_end_date').datepicker();
</script>