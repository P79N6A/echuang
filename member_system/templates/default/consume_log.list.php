<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="tabmenu">
    <?php include template('layout/submenu');?>
</div>
<form method="post" action="" target="_self">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="search-form">
        <tr>
            <!-- <td>
                <a nctype="del" href="javascript:void(0);" class="ncbtn  ncbtn-mint">
                    <i class="icon-credit-card"></i>
                    消费列表
                </a>
            </td> -->
            <td>&nbsp;</td>
            <th>兑换时间</th>
            <td class="w240"><input type="text" class="text w70" name="query_start_date" id="query_start_date" value="<?php echo $output['query_start_date']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label>&nbsp;&#8211;&nbsp;<input id="query_end_date" class="text w70" type="text" name="query_end_date" value="<?php echo $output['query_end_date']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label></td>
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
        <th class="w100">消费单号</th>
        <th class="w100">消费金豆数</th>
        <th class="w100">金券</th>
        <th class="w100">消费时间</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($output['list']) > 0): ?>
        <?php foreach ($output['list'] as $v): ?>
        <tr>
            <td><?php echo $v['cl_sn']; ?></td>
            <td><?php echo $v['cl_amount']; ?></td>
            <td><?php echo $v['elg_amount']; ?></td>
            <td><?php echo date('Y-m-d H:i:s', $v['cl_addtime']); ?></td>
        </tr>
        <?php endforeach?>
    <?php else: ?>
    <tr>
        <td colspan="20" class="norecord">
            <div class="warning-option">
                <i class="icon-warning-sign"></i>
                <span><?php echo $lang['no_record']; ?></span>
            </div>
        </td>
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
