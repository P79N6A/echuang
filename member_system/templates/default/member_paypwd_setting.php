<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="tabmenu">
    <?php include template('layout/submenu');?>
</div>
<div class="ncsc-form-default">
    <form id="paypwd_setting_form" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="form_submit" value="ok">
        <dl>
            <dt><i class="required"></i>手机号码：</dt>
            <dd>
                <span><?php echo $output['info']['member_mobile'] ?></span>
            </dd>
        </dl>
        <dl>
            <dt>支付密码：</dt>
            <dd>
                <input class="w400 text" name="member_paypwd" type="password" id="member_paypwd" value="" maxlength="30">
                <span></span>
                <p>6-20位字符，可由英文、数字及标点符号组成。</p>
            </dd>
        </dl>

        <dl>
            <dt>确认密码：</dt>
            <dd>
                <input class="w400 text" name="member_paypwd_confirm" type="password" id="member_paypwd_confirm" value="" maxlength="30">
                <span></span>
                <p>6-20位字符，可由英文、数字及标点符号组成。</p>
            </dd>
        </dl>
        <div class="bottom">
            <label class="submit-border">
                <input type="submit" class="submit" value="确认">
            </label>
        </div>
    </form>
</div>
<!-- <script type="text/javascript">
    $("#register_form").validate({

    })
</script> -->