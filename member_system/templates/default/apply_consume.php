<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="tabmenu">
    <?php include template('layout/submenu');?>
</div>
<div class="ncsc-form-default">
    <form id="add_form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="form_submit" value="ok" />
        <dl>
            <dt>金豆消费数：</dt>
            <dd>
                <input class="w400 text" name="consume_amount" type="text" id="consume_amount" value="" maxlength="30">
                <span></span>
            </dd>
        </dl>
        <dl>
            <dt>支付密码：</dt>
            <dd>
                <input class="w400 text" name="pay_password" type="password" id="pay_password" value="" maxlength="30">
                <span></span>
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
