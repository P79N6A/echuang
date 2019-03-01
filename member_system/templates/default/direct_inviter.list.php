<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="tabmenu">
    <?php include template('layout/submenu');?>
</div>
<!--<form method="get" action="index.php" target="_self">-->
<!--    <table class="search-form">-->
<!--        <input type="hidden" name="act" value="member_info" />-->
<!--        <input type="hidden" name="op" value="index" />-->
<!--        <tr>-->
<!--            <td>&nbsp;</td>-->
<!--            <th>注册时间</th>-->
<!--            <td class="w240"><input type="text" class="text w70" name="query_start_date" id="query_start_date" value="--><?php //echo $_GET['query_start_date']; ?><!--" /><label class="add-on"><i class="icon-calendar"></i></label>&nbsp;&#8211;&nbsp;<input id="query_end_date" class="text w70" type="text" name="query_end_date" value="--><?php //echo $_GET['query_end_date']; ?><!--" /><label class="add-on"><i class="icon-calendar"></i></label></td>-->
<!--            <th>-->
<!--                <select name="groupbuy_vr" class="w90">-->
<!--                    <option value="0">会员ID</option>-->
<!--                    <option value="1">会员手机</option>-->
<!--                    <option value="2">真实姓名</option>-->
<!--<!--                    <option value="3">接点人手机号</option>-->
<!--                </select>-->
<!--            </th>-->
<!--            <td class="w160"><input type="text" class="text w150" name="order_sn" value="--><?php //echo $_GET['order_sn']; ?><!--" /></td>-->
<!--            <td class="w70 tc"><label class="submit-border">-->
<!--                    <input type="submit" class="submit" value="搜索" />-->
<!--                </label>-->
<!--            </td>-->
<!--        </tr>-->
<!--    </table>-->
<!--</form>-->
<table class="ncsc-default-table order">
    <thead>
        <tr>
<!--            <th class="w100">会员ID</th>-->
            <th class="w100">会员手机</th>
            <th class="w100">真实姓名</th>
            <th class="w100">注册日期</th>
            <th class="w120">会员等级</th>
            <th class="w120">状态</th>
<!--            <th class="w120">接点人手机</th>-->
        </tr>
    </thead>
    <?php if (count($output['list']) > 0): ?>
        <?php foreach ($output['list'] as $v): ?>
        <tbody>
            <tr>
				<?php if (!isset($v['member_id'])){continue;} ?>
                <td><?php echo $v['member_mobile']; ?></td>
                <td><?php echo $v['member_name']; ?></td>
                <td><?php echo date('Y-m-d H:i:s', $v['reg_time']); ?></td>
                <td><?php echo $output['level'][$v['member_level']]? $output['level'][$v['member_level']]:'体验用户'?></td>
                <td><?php echo str_replace(array(0, 1), array('禁用', '正常'), $v['member_state']); ?></td>
<!--                <td>--><?php //echo $output['mobile'][$v['access_id']]; ?><!--</td>-->
            </tr>
        </tbody>
        <?php endforeach?>
    <?php else: ?>

    <?php endif?>

    <tfoot>
    <?php if (is_array($output['order_list']) and !empty($output['order_list'])) {?>
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
