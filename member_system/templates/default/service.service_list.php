<?php defined('In33hao') or exit('Access Invild!');?>
<div class="tabmenu">
    <?php include template('layout/submenu');?>
</div>
<table class="ncsc-default-table order">
    <thead>
        <tr>
            <th class="w100">申请人姓名</th>
            <th class="w100">申请人手机号</th>
            <th class="w100">申请时间</th>
            <th class="w100">申请状态</th>
            <th class="w100">备注</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($output['s_info'])): ?>
        <tr>
            <td>
                <?php echo $output['s_info']['slg_member_name']; ?>
            </td>
            <td>
                <?php echo $output['m_info'][$output['s_info']['slg_member_id']]; ?>
            </td>
            <td>
                <?php echo $output['s_info']['slg_addtime'] ? date('Y-m-d H:i:s', $output['s_info']['slg_addtime']) : ''; ?>
            </td>
            <td>
                <?php echo str_replace(array(0, 1, 2), array('待审核', '审核成功', '已拒绝'), $output['s_info']['slg_state']); ?>
            </td>
            <td>
				<?php echo $output['s_info']['slg_desc']; ?>
            </td>
        </tr>
        <?php else: ?>
        <tr>
            <td colspan="20" class="norecord">
                <div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record']; ?></span></div>
            </td>
        </tr>
        <?php endif?>
    </tbody>
</table>