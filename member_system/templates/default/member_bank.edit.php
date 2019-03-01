<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="tabmenu">
    <?php include template('layout/submenu');?>
</div>
<div class="ncsc-form-default">
    <form id="add_form" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="bank_id", value="<?php echo $output['bank_info']['mb_id']; ?>">
        <div>
            <dl>
                <dt>开户银行：</dt>
                <dd>
                    <select name="account_bank_name">
                        <?php echo $output['html']; ?>
                    </select>
                    <span></span>
                </dd>
            </dl>
            <dl>
                <dt>开户支行：</dt>
                <dd>
                    <input class="w300 text" name="account_branch" type="text" id="account_branch" value="<?php echo $output['bank_info']['mb_account_branch']; ?>" maxlength="30">
                    <span></span>
                </dd>
            </dl>
            <dl>
                <dt>开户名：</dt>
                <dd>
                    <input class="w300 text" name="account_name" type="text" id="account_name" value="<?php echo $output['bank_info']['mb_account_name']; ?>" maxlength="30">
                    <span></span>
                </dd>
            </dl>
            <dl>
                <dt>银行账号：</dt>
                <dd>
                    <input class="w300 text" name="account_num" type="text" id="account_num" value="<?php echo $output['bank_info']['mb_account_num']; ?>" maxlength="30">
                    <span></span>
                </dd>
            </dl>
            <div class="bottom">
                <label class="submit-border">
                    <input type="submit" class="submit" value="确认修改">
                </label>
            </div>
        </div>
    </form>
</div>
<!-- <script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery-ui/i18n/zh-CN.js" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL; ?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
 -->