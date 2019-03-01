<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>红包审核</h3>
                <h5>红包审核</h5>
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
            <li>通过红包审核管理，你可以进行查看所有用户的红包</li>
            <li>你可以根据条件搜索，然后选择相应的操作</li>
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
                                    <option value="red_envelopes_order_number">红包单号</option>
                                </select>
                            </label>
                            <label>
                                <input type="text" value="" placeholder="请输入关键字" name="keyword" class="s-input-txt">
                            </label>
                        </dd>
                    </dl>
                    <dl>
                        <dt>添加时间</dt>
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
            url: 'index.php?act=redbag_issue&op=get_xml',
            colModel : [
                {display: '操作', name: 'operation', width : 200, sortable : false, align: 'center'},
                {display: '红包单号', name: 'red_envelopes_order_number', width : 130, sortable : false, align: 'center'},
                {display: '用户手机', name: 'member_mobile', width : 130, sortable : false, align: 'center'},
                {display: '红包金额', name: 'money', width : 100, sortable : false, align: 'center'},
                {display: '红包个数', name: 'red_envelopes_location_num', width : 80, sortable : false, align: 'center'},
                {display: '定位范围', name: 'area_name', width : 160, sortable : false, align: 'center'},
                {display: '年龄阶段', name: 'age', width : 160, sortable : false, align: 'center'},
                {display: '爱好', name: 'name', width : 80, sortable : false, align: 'center'},
                {display: '性别', name: 'gender', width : 80, sortable : false, align: 'center'},
                {display: '图片', name: 'red_envelopes_picture', width : 80, sortable : false, align: 'center'},
                {display: '广告词', name: 'red_envelopes_content', width : 160, sortable : false, align: 'center'},
                {display: '链接', name: 'link', width : 200, sortable : false, align: 'center'}
            ],
            buttons : [
                {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'xls', bclass : 'xls', title : '将选定行数据导出execl文件', onpress : fg_operation }
            ],
            searchitems: [
                {display: '会员手机', name : 'member_mobile'},
                {display: '红包单号', name: 'red_envelopes_order_number'}
            ],
            sortname: "red_envelopes_id",
            sortorder: "desc",
            title: '红包审核列表'
        });

    });

    function fg_operation(name, bDiv) {
        if (name == 'xls') {
            if ($('.trSelected', bDiv).length == 0) {
                if (!confirm('您确定要下载全部数据吗？')) {
                    return false;
                }
            }
            window.location.href = 'index.php?act=redbag_issue&op=export_step1';
        }
    }
    // 高级搜索提交
    $('#ncsubmit').click(function() {
        $("#flexigrid").flexOptions({ url: 'index.php?act=redbag_issue&op=get_xml&' + $("#formSearch").serialize(), query: '', qtype: '' }).flexReload();
    });

    // 高级搜索重置
    $('#ncreset').click(function() {
        $("#flexigrid").flexOptions({ url: 'index.php?act=redbag_issue&op=get_xml' }).flexReload();
        $("#formSearch")[0].reset();
    });

    $('#query_start_date').datepicker();
    $('#query_end_date').datepicker();
</script>