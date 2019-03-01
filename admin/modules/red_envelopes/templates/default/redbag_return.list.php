<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>升级返现记录</h3>
                <h5>体验用户申请成为会员列表管理</h5>
            </div>
        </div>
    </div>
        <!-- 操作说明 -->
        <div class="explanation" id="explanation">
            <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
                <h4 title="">操作说明</h4>
                <span id="explanationZoom" title=""></span>
            </div>
            <ul>
                <li>通过会员申请管理列表，你可以进行查看，管理会员资料</li>
                <li>你可以根据条件搜索会员，然后查看相应的会员资料</li>
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
                                    <option value="order_number">订单号</option>
                                    <option value="member_name_return">用户名称</option>
                                    <option value="member_mobile_return">用户手机</option>
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
        $(function(){
            $("#flexigrid").flexigrid({
                url: 'index.php?act=upgrade_return&op=get_xml',
                colModel : [
                    {display: '单号', name : 'order_number', width : 200, sortable : true, align: 'center'},
                    {display: '用户名称', name : 'member_name_return', width : 100, sortable : true, align: 'center'},
                    {display: '用户手机', name : 'member_mobile_return', width : 200, sortable : true, align: 'center'},
                    {display: '升级用户', name : 'member_mobile', width : 200, sortable : true, align: 'center'},
                    {display: '金额（元）', name : 'money', width : 100, sortable : true, align: 'center'},
                    {display: '时间', name : 'addtime', width : 200, sortable : true, align: 'center'}
                ],
                buttons : [
                    {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '导出Excel文件', onpress : fg_operation }
                ],
                searchitems : [
                    {display: '订单号', name : 'order_number'},
                    {display: '用户名称', name : 'member_name_return'},
                    {display: '用户手机', name : 'member_mobile_return'}
                ],
                sortname: "addtime",
                sortorder: "desc",
                title: '升级返现管理列表'
            });
        });

        function fg_operation(name, bDiv) {
            if (name == 'csv') {
                if ($('.trSelected', bDiv).length == 0) {
                    if (!confirm('您确定要下载全部数据吗？')) {
                        return false;
                    }
                }
                var itemids = new Array();
                $('.trSelected', bDiv).each(function(i){
                    itemids[i] = $(this).attr('data-id');
                });
                fg_csv(itemids);
            }
        }
        function fg_csv(ids) {
            id = ids.join(',');
            window.location.href = $("#flexigrid").flexSimpleSearchQueryString()+'&op=export_step1&id=' + id;
        }
        // 高级搜索提交
        $('#ncsubmit').click(function() {
            $("#flexigrid").flexOptions({ url: 'index.php?act=upgrade_return&op=get_xml&' + $("#formSearch").serialize(), query: '', qtype: '' }).flexReload();
        });

        // 高级搜索重置
        $('#ncreset').click(function() {
            $("#flexigrid").flexOptions({ url: 'index.php?act=upgrade_return&op=get_xml' }).flexReload();
            $("#formSearch")[0].reset();
        });

        $('#query_start_date').datepicker();
        $('#query_end_date').datepicker();
    </script>