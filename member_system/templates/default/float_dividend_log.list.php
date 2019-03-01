<?php defined('In33hao') or exit('Access Invild');?>
<div class="tabmenu">
    <?php include template('layout/submenu');?>
</div>
<table class="ncsc-default-table order">
    <thead>
    <tr>
        <th class="w100">分红时间</th>
        <th class="w100">单号</th>
        <th class="w100">浮动分红率</th>
        <th class="w100">金券数量</th>
        <th class="w100">浮动分红</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($output['list']) > 0): ?>
        <?php foreach ($output['list'] as $v): ?>
        <tr>
            <td><?php echo $v['flg_addtime'] ? date('Y-m-d', $v['flg_addtime']) : '' ?></td>
            <td><?php echo $v['flg_sn']; ?></td>
            <td><?php echo $v['flg_dividend_rate']; ?></td>
            <td><?php echo $v['flg_equity_num']; ?></td>
            <td><?php echo $v['flg_amount']; ?></td>
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