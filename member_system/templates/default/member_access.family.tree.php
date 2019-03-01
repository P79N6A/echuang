<?php defined('In33hao') or exit('Access Invalid!');?>

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
            <td class="w160"><input type="text" class="text w120" name="member_mobile" value="<?php echo $output['member_mobile']; ?>" /></td>
            <td class="w70 tc"><label class="submit-border">
                    <input type="submit" class="submit" value="搜索" />
                </label>
            </td>
        </tr>
    </table>
</form> -->
<div class="familytree">
  	<div class="tree">
  		<?php echo $output['tree']; ?>
	</div>
 </div>
