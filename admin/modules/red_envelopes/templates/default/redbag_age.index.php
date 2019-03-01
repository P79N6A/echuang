<?php defined('In33hao') or exit('Access Invild!');?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3 class="left-top">年龄阶段</h3>
            </div>
        </div>
    </div>
    <div id="flexigrid"></div>

</div>
<script type="text/javascript">
    $(function() {
        $("#flexigrid").flexigrid({
            url: 'index.php?act=age&op=get_xml',
            colModel: [
                { display: '操作', name: 'operation', width: 120, sortable: false, align: 'center' },
                { display: '年龄段', name: 'age_name', width: 160, sortable: false, align: 'center' },
            ],
            buttons: [
                { display: '<i class="fa fa-plus"></i>新增年龄标签', name: 'add', bclass: 'add', title: '新增年龄阶段信息', onpress: fg_operation },
            ],
            sortname: "age_name",
            sortorder: "desc",
            title: '年龄阶段列表'
        });
    });
    function fg_operation(name, bDiv) {
        if (name == 'add') {
            window.location.href = 'index.php?act=age&op=age_add';
        }
    }

    function fg_delete(id) {
        if(confirm('删除后将不能恢复，确认删除这项吗？')){
            window.location.href = 'index.php?act=age&op=agedel&age_id='+id;
        }
    }

</script>