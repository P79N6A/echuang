<?php defined('In33hao') or exit('Access Invalid!');?>
<script src="<?php echo RESOURCE_SITE_URL ?>/js/tree/jQuery/jQuery-2.1.4.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL ?>/js/tree/jquery-ui/jquery-ui.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL ?>/js/tree/jquery-treetable/jquery.treetable.js"></script>
<link href="<?php echo RESOURCE_SITE_URL ?>/js/tree/jquery-treetable/jquery.treetable.css" rel="stylesheet"  type="text/css">
<div class="tabmenu">
    <?php include template('layout/submenu');?>
</div>
<!-- <form method="post" action="" target="_self">
    <table class="search-form">
		<input type="hidden" name="form_submit" value="ok" />
        <tr style="float: right;">
            <th>
                <select name="group_by" class="w90">
                    <option value="0">会员手机</option>
                </select>
            </th>
            <td class="w160"><input type="text" class="text w120" name="member_mobile" value="<?php echo $_POST['member_mobile']; ?>" /></td>
            <td class="w70 tc"><label class="submit-border">
                    <input type="submit" class="submit" value="搜索" />
                </label>
            </td>
        </tr>
    </table>
</form> -->
<div id="familytree">
  	<table id="mytable" class="treetable table table-hover">
        <?php echo $output['tree']; ?>
    </table>
</div>
<script type="text/javascript">
    // 树表
    $(document).ready(function()  {
        $("#mytable").treetable({
            indent: 30,  //折叠缩进
            expandable: true,   //启用折叠
            initialState:"collapsed"//默认展开
        });
    });
</script>
