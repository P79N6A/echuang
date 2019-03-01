<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="tabmenu">
    <?php include template('layout/submenu');?>
</div>
<div class="ncsc-form-default">
    <form id="add_form" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="form_submit" value="ok">
<!--        <dl>-->
<!--            <dt><i class="required"></i>用户ID：</dt>-->
<!--            <dd>-->
<!--                <span>--><?php //echo $output['info']['member_id'] ?><!--</span>-->
<!--            </dd>-->
<!--        </dl>-->
        <dl>
            <dt><i class="required"></i>手机号码：</dt>
            <dd>
                <span><?php echo $output['info']['member_mobile'] ?></span>
            </dd>
        </dl>
        <dl>
            <dt><i class="required"></i>姓&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名：</dt>
            <dd>
                <span><?php echo $output['info']['member_name'] ?></span>
            </dd>
        </dl>
        <dl>
            <dt>登录密码：</dt>
            <dd>
                <input class="w400 text" name="member_pwd" type="password" id="member_pwd" value="" maxlength="30" placeholder="填空表示不修改当前密码"/>
                <span></span>
                <p>6-20位字符，可由英文、数字及标点符号组成。</p>
            </dd>
        </dl>

        <dl>
            <dt>支付密码：</dt>
            <dd>
                <input class="w400 text" name="member_paypwd" type="password" id="member_paypwd" value="" maxlength="30" placeholder="填空表示不修改当前密码"/>
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
