<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="tabmenu">
    <?php include template('layout/submenu');?>
<!--    --><?php //if (empty($output['list'])): ?>
    <a class="ncbtn ncbtn-mint" href="index.php?act=member_info&op=bank_account_add" style="margin-right: 50px">+添加银行卡</a>
<!--    --><?php //endif?>
</div>
<table class="ncsc-default-table order">
    <thead>
    <tr>
        <th class="w100">开户名</th>
        <th class="w100">开户银行</th>
        <th class="w100">开户支行</th>
        <th class="w100">银行账号</th>
        <th class="w120">操作</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($output['list']) > 0): ?>
        <?php foreach ($output['list'] as $v): ?>
        <tr>
            <td><?php echo $v['mb_account_name']; ?></td>
            <td><?php echo $v['mb_account_bank_name']; ?></td>
            <td><?php echo $v['mb_account_branch']; ?></td>
            <td><?php echo $v['mb_account_num']; ?></td>
            <td>
                <a href="index.php?act=member_info&op=bank_account_edit&id=<?php echo $v['mb_id']; ?>" class="ncbtn ncbtn-mint" title="编辑银行卡">
                    <i class="icon-book"></i>
                    编辑
                </a>
                <span>
                    <a nctype="del" href="index.php?act=member_info&op=bank_account_del&id=<?php echo $v['mb_id']; ?>" class="ncbtn  ncbtn-grapefruit" title="删除银行卡">
                        <i class="icon-trash"></i>
                        删除
                    </a>
                </span>
            </td>
        </tr>
        <?php endforeach?>
    <?php else: ?>
    <tr>
        <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record']; ?></span></div>
        </td>
    </tr>
    <?php endif?>


    </tbody>
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
