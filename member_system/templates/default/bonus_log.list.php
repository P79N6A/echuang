<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="tabmenu">
    <?php include template('layout/submenu');?>
</div>
<form method="post" action="" target="_self">
    <table class="search-form">
        <input type="hidden" name="form_submit" value="ok" />
        <tr>
            <td>&nbsp;</td>
            <th>单号</th>
            <th>
            <input type="text" name="dr_no">
            </th>
            <th>奖励类型</th>
            <th>
            <select name="type" class="w90">
            <option value="0" <?php echo ($output['bonus_type'] == 0) ? 'selected' : ''; ?>>选择类型</option>
            <option value="1" <?php echo ($output['bonus_type'] == 1) ? 'selected' : ''; ?>>级差奖励</option>
            <option value="2" <?php echo ($output['bonus_type'] == 2) ? 'selected' : ''; ?>>平级奖励</option>
            <option value="3" <?php echo ($output['bonus_type'] == 3) ? 'selected' : ''; ?>>直推奖励</option>
            </select>
            </th>
            <th>奖励时间</th>
            <td class="w240"><input type="text" class="text w70" name="query_start_date" id="query_start_date" value="<?php echo $output['query_start_date']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label>&nbsp;&#8211;&nbsp;<input id="query_end_date" class="text w70" type="text" name="query_end_date" value="<?php echo $output['query_end_date']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label></td>
            <td class="w70 tc">
                <label class="submit-border">
                    <input type="submit" class="submit" value="搜索" />
                </label>
            </td>
        </tr>
    </table>
</form>
<table class="ncsc-default-table order">
    <thead>
        <tr>
            <th class="w100">奖励时间</th>
            <th class="w100">单号</th>
            <th class="w100">描述</th>
            <th class="w100">金额</th>
            <th class="w100">类型</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($output['list'])): ?>
            <?php foreach ($output['list'] as $v): ?>
            <tr>
                <td><?php echo date('Y-m-d', $v['add_time']); ?></td>
                <td><?php echo $v['dr_no']; ?></td>
                <td><?php echo $v['content']; ?></td>
                <td><?php echo $v['prize'] ?></td>
                <td><?php echo str_replace(array("diff","eql","direct"),array("级差奖励","平级奖励","直推奖励"),$v['type']) ?></td>
            </tr>
            <?php endforeach?>
        <?php else: ?>
        <tr>
            <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record']; ?></span></div></td>
        </tr>
        <?php endif?>


    </tbody>
    <tfoot>
    <?php if (is_array($output['list']) and !empty($output['list'])) {?>
        <tr>
            <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
        </tr>
    <?php }?>
    </tfoot>
</table>
<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery-ui/i18n/zh-CN.js" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL; ?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script type="text/javascript">
    $(function(){
        $('#query_start_date').datepicker({dateFormat: 'yy-mm-dd'});
        $('#query_end_date').datepicker({dateFormat: 'yy-mm-dd'});
        $('.checkall_s').click(function(){
            var if_check = $(this).attr('checked');
            $('.checkitem').each(function(){
                if(!this.disabled)
                {
                    $(this).attr('checked', if_check);
                }
            });
            $('.checkall_s').attr('checked', if_check);
        });
        $('#skip_off').click(function(){
            url = location.href.replace(/&skip_off=\d*/g,'');
            window.location.href = url + '&skip_off=' + ($('#skip_off').attr('checked') ? '1' : '0');
        });
    });
</script>
