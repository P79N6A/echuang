<?php defined('In33hao') or exit('Access Invild!');?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3 class="left-top">红包爱好</h3>
            </div>
        </div>
    </div>
    <div id="flexigrid"></div>
</div>
<script type="text/javascript">
    $(function() {
        $("#flexigrid").flexigrid({
            url: 'index.php?act=interest&op=get_xml',
            colModel: [
                { display: '操作', name: 'operation', width: 120, sortable: false, align: 'center' },
                { display: '爱好', name: 'interest_name', width: 160, sortable: false, align: 'center' },
            ],
            buttons: [
                { display: '<i class="fa fa-plus"></i>新增红包爱好', name: 'add', bclass: 'add', title: '新增红包标签信息', onpress: fg_operation },
            ],
            sortname: "interest_name",
            sortorder: "desc",
            title: '红包爱好列表'
        });
    });
    function fg_operation(name, bDiv) {
        if (name == 'add') {
            window.location.href = 'index.php?act=interest&op=interest_add';
        }
    }
    function fg_delete(id) {
        if(confirm('删除后将不能恢复，确认删除这项吗？')){
            window.location.href = 'index.php?act=interest&op=interestdel&interest_id='+id;
        }
    }
</script>