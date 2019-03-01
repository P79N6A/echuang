<?php defined('In33hao') or exit('Access Invild!');?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>银行列表</h3>
                <h5>商城可支持的银行列表</h5>
            </div>
        </div>
    </div>
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom">
            <h4 title="<?php echo $lang['nc_prompts_title']; ?>"><?php echo $lang['nc_prompts']; ?></h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span']; ?>"></span> </div>
        <ul>
            <li></li>
        </ul>
    </div>
    <div id="flexigrid"></div>
</div>
<script type="text/javascript">
$(function() {
    $("#flexigrid").flexigrid({
        url: 'index.php?act=bank&op=get_xml',
        colModel: [
            { display: '操作', name: 'operation', width: 120, sortable: false, align: 'center' },
            { display: '银行名称', name: 'bank_name', width: 160, sortable: false, align: 'center' },
            { display: '银行简称', name: 'bank_abbreviation', width: 160, sortable: false, align: 'center' },
            { display: '图标', name: 'bank_logo', width: 160, sortable: false, align: 'center' },
            { display: '当前状态', name: 'bank_state', width: 160, sortable: false, align: 'center' },
        ],
        buttons: [
            { display: '<i class="fa fa-plus"></i>新增银行', name: 'add', bclass: 'add', title: '新增银行信息', onpress: fg_operation },
        ],
        sortname: "bank_name",
        sortorder: "desc",
        title: '银行列表'
    });
});
function fg_operation(name, bDiv) {
    if (name == 'add') {
        window.location.href = 'index.php?act=bank&op=bankAdd';
    }
}
</script>