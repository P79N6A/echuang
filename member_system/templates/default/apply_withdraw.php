<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="tabmenu">
    <?php include template('layout/submenu');?>
</div>
<div class="ncsc-form-default">
    <form id="add_form" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="form_submit" value="ok" />
        <dl>
            <dt>提现金额：</dt>
            <dd>
                <input class="w400 text" name="withdraw_amount" type="text" id="withdraw_amount" value="" maxlength="30">
            </dd>
        </dl>
        <dl>
            <dt>提现银行账号：</dt>
            <dd>
                <select name="account_id" id="account_id">
                    <option value="">请选择提现银行账号</option>
                    <?php foreach ($output['bank_list'] as $v): ?>
                    <option value="<?php echo $v['mb_id']; ?>"><?php echo $v['mb_account_bank_name'] . '(' . formatBankCardNo($v['mb_account_num']) . ')'; ?></option>
                    <?php endforeach?>
                </select>
            </dd>
        </dl>
        <dl>
            <dt>支付密码：</dt>
            <dd>
                <input class="w400 text" name="pay_password" type="password" id="pay_password" value="" maxlength="30">
            </dd>
        </dl>
        <div class="bottom">
            <label class="submit-border">
                <input type="submit" class="submit" value="确认提交">
            </label>
        </div>
    </form>
</div>
<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery-ui/i18n/zh-CN.js" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL; ?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
