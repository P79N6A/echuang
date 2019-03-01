<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>城主记录</h3>
                <h5></h5>
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
            <li>通过城主记录列表，你可以进行查看，记录城主资料</li>
            <li>你可以根据条件搜索城主记录，然后查看相应的城主记录资料</li>
        </ul>
    </div>
    <div id="flexigrid"></div>
</div>
    <script type="text/javascript">
        $(function(){
            $("#flexigrid").flexigrid({
                url: 'index.php?act=city_price&op=get_xml',
                colModel : [
                    {display: '操作', name : 'operation', width : 200, sortable : true, align: 'center'},
                    {display: '省', name : 'province', width : 200, sortable : true, align: 'center'},
                    {display: '市', name : 'city', width : 100, sortable : true, align: 'center'},
                    {display: '区', name : 'area', width : 200, sortable : true, align: 'center'},
                    {display: '竟拍原价', name : 'price', width : 200, sortable : true, align: 'center'},
                ],
                buttons : [
                    {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '导出Excel文件', onpress : fg_operation },
                    {display: '<a href="index.php?act=city_price&op=cityprice_push"><i class="fa fa-file-excel-o"></i>添加</a>'}
                ],
                searchitems : [
                    {display: '省', name : 'province'},
                    {display: '市', name : 'city'},
                    {display: '区', name : 'area'}
                ],
                sortname: "city_price_id",
                sortorder: "desc",
                title: '城主原竞价管理列表'
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
    </script>