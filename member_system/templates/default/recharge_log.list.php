<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="tabmenu">
    <?php include template('layout/submenu');?>
</div>
<form method="post" action="" target="_self">
    <table class="search-form">
        <input type="hidden" name="form_submit" value="ok" />
        <tr>
            <a nctype="del" href="index.php?act=member_property&op=recharge_apply" class="ncbtn  ncbtn-mint">
                <i class="icon-credit-card"></i>
                申请充值
            </a>
            <td>&nbsp;</td>
            <th>充值时间</th>
            <td class="w240"><input type="text" class="text w70" name="query_start_date" id="query_start_date" value="<?php echo $output['query_start_date']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label>&nbsp;&#8211;&nbsp;<input id="query_end_date" class="text w70" type="text" name="query_end_date" value="<?php echo $output['query_end_date']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label></td>
            <th>
                <select name="groupbuy_vr" class="w90">
                    <option value="0">支付宝</option>
                </select>
            </th>
            <td class="w70 tc"><label class="submit-border">
                    <input type="submit" class="submit" value="搜索" />
                </label>
            </td>
        </tr>
    </table>
</form>
<table class="ncsc-default-table order">
    <thead>
    <tr>
        <th class="w100">充值单号</th>
        <th class="w100">充值金额</th>
        <th class="w100">支付方式</th>
        <th class="w100">充值状态</th>
        <th class="w100">充值时间</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($output['list']) > 0): ?>
        <?php foreach ($output['list'] as $v): ?>
        <tr>
            <td><?php echo $v['rl_sn']; ?></td>
            <td><?php echo $v['rl_amount']; ?></td>
            <td><?php echo str_replace(array('alipay', 'wxpay'), array('支付宝', '微信'), $v['rl_recharge_type']); ?></td>
            <td><?php echo str_replace(array(0, 1), array('待支付', '已支付'), $v['rl_state']); ?></td>
            <td><?php echo $v['rl_paytiem']; ?></td>
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
