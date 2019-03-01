<?php defined('In33hao') or exit('Access Invild!');?>
<div class="tabmenu">
    <?php include template('layout/submenu');?>
</div>
<div class="ncsc-form-default">
    <form id="add_form" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="form_submit" value="ok" />
        <dl>
            <dt><i class="required"></i>姓名：</dt>
            <dd>
                <span><?php echo $output['info']['member_name'] ?></span>
            </dd>
        </dl>
        <dl>
            <dt><i class="required"></i>手机号码：</dt>
            <dd>
                <span><?php echo $output['info']['member_mobile'] ?></span>
            </dd>
        </dl>
        <div class="bottom">
            <label class="submit-border">
                <input type="submit" class="submit" value="申请服务中心">
            </label>
        </div>
    </form>
</div>