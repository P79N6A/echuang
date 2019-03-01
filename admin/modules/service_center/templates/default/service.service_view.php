<?php defined('In33hao') or exit('Access Invild!');?>
<div class="ncap-form-default">
    <dl class="row">
        <dt class="tit">
            <label>会员名</label>
        </dt>
        <dd class="opt">
            <?php echo $output['namearr'][$output['s_info']['slg_member_id']]; ?>
            <p class="notic"></p>
        </dd>
    </dl>
    <dl class="row">
        <dt class="tit">
            <label>会员手机号</label>
        </dt>
        <dd class="opt">
            <?php echo $output['mobilearr'][$output['s_info']['slg_member_id']]; ?>
            <p class="notic"></p>
        </dd>
    </dl>
    <dl class="row">
        <dt class="tit">
            <label>申请时间</label>

        </dt>
        <dd class="opt">
            <?php echo $output['s_info']['slg_addtime'] ? date('Y-m-d H:i:s', $output['s_info']['slg_addtime']) : ''; ?>
            <p class="notic"></p>
        </dd>
    </dl>
    <dl class="row">
        <dt class="tit">
            <label>申请状态</label>
        </dt>
        <dd class="opt">
            <?php echo str_replace(array(0, 1), array('待审核', '审核成功'), $output['s_info']['slg_state']); ?>
            <p class="notic"></p>
        </dd>
    </dl>
    <?php if ($output['s_info']['slg_state']): ?>
    <dl class="row">
        <dt class="tit">
            <label>审核时间</label>
        </dt>
        <dd class="opt">
            <?php echo date('Y-m-d H:i:s', $output['s_info']['slg_audittime']); ?>
            <p class="notic"></p>
        </dd>
    </dl>
    <?php endif?>

</div>