<div class="page">
	<div class="fixed-bar">
      <div class="item-title">
          <div class="subject">
            <h3>积分互赠管理</h3>
            <h5>会员系统积分互赠管理</h5>
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
          <li>通过积分互赠管理，你可以进行查看所有会员的积分互赠情况</li>
          <li>你可以根据条件搜索，然后选择相应的操作</li>
      </ul>
    </div>
    <div id="flexigrid"></div>
<!--    <div class="ncap-search-ban-s" id="searchBarOpen"><i class="fa fa-search-plus"></i>高级搜索</div>-->
<!--    <div class="ncap-search-bar">-->
<!--        <div class="handle-btn" id="searchBarClose"><i class="fa fa-search-minus"></i>收起边栏</div>-->
<!--        <div class="title">-->
<!--            <h3>高级搜索</h3>-->
<!--        </div>-->
<!--        <form method="get" name="formSearch" id="formSearch">-->
<!--            <div id="searchCon" class="content">-->
<!--                <div class="layout-box">-->
<!--                    <dl>-->
<!--                        <dt>关键字搜索</dt>-->
<!--                        <dd>-->
<!--                            <label>-->
<!--                                <select class="s-select" name="keyword_type">-->
<!--                                    <option selected="selected" value="">-请选择-</option>-->
<!--                                    <option value="member_mobile">会员手机</option>-->
<!--                                    <option value="member_name">会员姓名</option>-->
<!--                                </select>-->
<!--                            </label>-->
<!--                            <label>-->
<!--                                <input type="text" value="" placeholder="请输入关键字" name="keyword" class="s-input-txt">-->
<!--                            </label>-->
<!--                        </dd>-->
<!--                    </dl>-->
<!--                    <dl>-->
<!--                        <dt>时间</dt>-->
<!--                        <dd>-->
<!--                            <label>-->
<!--                                <input readonly id="query_start_date" placeholder="请选择起始时间" name=query_start_date value="" type="text" class="s-input-txt" />-->
<!--                            </label>-->
<!--                            <label>-->
<!--                                <input readonly id="query_end_date" placeholder="请选择结束时间" name="query_end_date" value="" type="text" class="s-input-txt" />-->
<!--                            </label>-->
<!--                        </dd>-->
<!--                    </dl>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="bottom"><a href="javascript:void(0);" id="ncsubmit" class="ncap-btn ncap-btn-green mr5">提交查询</a><a href="javascript:void(0);" id="ncreset" class="ncap-btn ncap-btn-orange" title="撤销查询结果，还原列表项所有内容"><i class="fa fa-retweet"></i>--><?php //echo $lang['nc_cancel_search']; ?><!--</a></div>-->
<!--        </form>-->
<!--    </div>-->
</div>
<script type="text/javascript">
$(function() {
    $("#flexigrid").flexigrid({
        url: 'index.php?act=equity&op=get_xml',
        colModel : [
               {display: '操作', name: 'operation', width : 160, sortable : false, align: 'center'},
            {display: '转赠人手机', name: 'member_mobile', width : 130, sortable : false, align: 'center'},
            {display: '转赠人', name: 'member_name', width : 160, sortable : false, align: 'center'},
            {display: '接收人手机', name: 'receiver_mobile', width : 160, sortable : false, align: 'center'},
            {display: '接收人', name: 'receiver_name', width : 160, sortable : false, align: 'center'},
            {display: '数量', name: 'integral', width : 160, sortable : false, align: 'center'},
            {display: '转赠时间', name: 'add_time', width : 160, sortable : false, align: 'center'},
            {display: '备注', name: 'remarks', width : 160, sortable : false, align: 'center'},
        ],
        buttons : [
            {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'xls', bclass : 'xls', title : '将选定行数据导出execl文件', onpress : fg_operation }
            ],
        searchitems: [
            {display: '会员手机', name : 'member_mobile'},
            {display: '会员姓名', name: 'elg_member_name'},
        ],
        sortname: "elg_addtime",
        sortorder: "desc",
        title: '积分互赠管理列表'
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
    $("#flexigrid").flexOptions({ url: 'index.php?act=equity&op=get_xml&' + $("#formSearch").serialize(), query: '', qtype: '' }).flexReload();
});

// 高级搜索重置
$('#ncreset').click(function() {
    $("#flexigrid").flexOptions({ url: 'index.php?act=equity&op=get_xml' }).flexReload();
    $("#formSearch")[0].reset();
});
$('#query_start_date').datepicker();
$('#query_end_date').datepicker();
</script>