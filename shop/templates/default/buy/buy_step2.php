<?php defined('In33hao') or exit('Access Invild!');?>
<div class="ncc-main">
    <div class="ncc-title">
        <h3><?php echo $lang['cart_index_payment']; ?></h3>
        <h5>订单详情内容可通过查看<a href="index.php?act=member_order" target="_blank">我的订单</a>进行核对处理。</h5>
    </div>
    <form action="index.php?act=payment&op=real_order" method="POST" id="buy_form">
        <input type="hidden" name="pay_sn" value="<?php echo $output['pay_info']['pay_sn']; ?>" />
        <input type="hidden" id="payment_code" name="payment_code" value="" />
        <input type="hidden" value="" name="password_callback" id="password_callback" />
        <div class="ncc-receipt-info">
            <div class="ncc-receipt-info-title">
                <h3><?php echo $output['pay']['order_remind']; ?><?php echo $output['pay']['pay_online_amount'] > 0 ? "应付金额：<strong>" . ncPriceFormat($output['pay']['pay_diff_online_amount']) . "</strong>元" : null; ?>
                </h3>
            </div>
            <table class="ncc-table-style">
                <thead>
                    <tr>
                        <th class="w50"></th>
                        <th class="w200 tl">订单号</th>
                        <th class="tl w150">支付方式</th>
                        <th class="tl">金额(元)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($output['order_list']) > 1) {?>
                    <tr>
                        <th colspan="20">由于您的商品由不同商家发出，此单将分为
                            <?php echo count($output['order_list']); ?>个不同子订单配送！
                        </th>
                    </tr>
                    <?php }?>
                    <?php foreach ($output['order_list'] as $order_info) {?>
                    <tr>
                        <td></td>
                        <td class="tl">
                            <?php echo $order_info['order_sn']; ?>
                        </td>
                        <td class="tl">
                            <?php echo $order_info['payment_type']; ?>
                        </td>
                        <td class="tl">
                            <?php echo $order_info['order_amount']; ?>
                        </td>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
            <!-- 可用金豆、银豆 -->
            <dl class="ncc-bean-pay">
                <?php if (!empty($output['pay']['payd_bean_amount'])) { ?>
                <dd>您已选择<?php echo $output['pay']['payd_bean_amount'] ? "使用金豆支付<em>" . ncPriceFormat($output['pay']['payd_bean_amount']) . "</em>元" : null; ?> 还需在线支付 <strong id="api_pay_amount"><?php echo ncPriceFormat($output['pay']['pay_diff_online_amount']); ?></strong>元。</dd>
                <?php }?>
                <?php if ($output['pay']['if_show_bean_show'] || $output['pay']['if_show_silver_show'] || $output['pay']['if_show_balance_show']) {?>
                    <span style="font-size: medium">使用余额支付</span>（目前你需支付 <strong id="api_pay_amount"><?php echo ncPriceFormat($output['pay']['pay_diff_online_amount']); ?></strong>元。)
                <?php if ($output['pay']['if_show_bean_show']) {?>
                <dd>
                    <label>
                        <input type="checkbox" class="checkbox" value="1" name="bean_pay"> 使用金豆支付
                    </label> （可用余额：<em><?php echo ncPriceFormat($output['pay']['member_bean']); ?></em>元）
                </dd>
                <?php }?>
                <?php if ($output['pay']['if_show_silver_show']) {?>
                <dd>
                    <label>
                        <input type="checkbox" class="checkbox" value="2" name="silver_pay"> 使用银豆支付
                    </label> （可用余额：<em><?php echo ncPriceFormat($output['pay']['member_silver']); ?></em>元）
                </dd>
                <?php }?>
                <?php if ($output['pay']['if_show_balance_show']) {?>
                    <dd>
                        <label>
                            <input type="checkbox" class="checkbox" value="3" name="balance_pay"> 使用余额支付
                        </label> （可用余额：<em><?php echo ncPriceFormat($output['pay']['member_balance']); ?></em>元）
                    </dd>
                <?php }?>
                <dd id="bean_password" style="display: none">请输入支付密码
                    <input type="password" value="" name="password" id="pay-password" maxlength="35" autocomplete="off" />
                    <a href="javascript:void(0);" class="ncbtn-mini ncbtn-bittersweet" id="bean_pay_submit"><i class="icon-shield"></i>验证支付密码</a>
                    <?php if (!$output['pay']['member_paypwd']) {?> 还未设置支付密码，
                    <a href="<?php echo urlMemberSystem('member_info', 'change_paypwd'); ?>" target="_blank">马上设置</a>
                    <?php }?>
                </dd>
                <?php }?>

            </dl>

        </div>
        <?php if (!empty($output['payment_list'])) {?>
        <div class="ncc-online-receipt-info">
            <div class="ncc-receipt-info-title">
                <h3>选择在线支付</h3>
            </div>
            <ul class="ncc-payment-list">
                <?php foreach ($output['payment_list'] as $val) {?>
                <li payment_code="<?php echo $val['payment_code']; ?>">
                    <label for="pay_<?php echo $val['payment_code']; ?>">
                        <i></i>
                        <div class="logo" for="pay_<?php echo $val['payment_id']; ?>"> <img src="<?php echo SHOP_TEMPLATES_URL; ?>/images/payment/<?php echo $val['payment_code']; ?>_logo.gif" /> </div>
                    </label>
                </li>
                <?php }?>
            </ul>
        </div>
        <?php }?>
        <?php if ($output['pay']['pay_amount_online'] > 0) {?>
        <div class="ncc-bottom"><a href="javascript:void(0);" id="next_button" class="pay-btn"><i class="icon-shield"></i>确认支付</a></div>
        <?php }?>
    </form>
</div>
<script type="text/javascript">
$(function() {
    $('.ncc-payment-list > li').on('click', function() {
        $('.ncc-payment-list > li').removeClass('using');
        if ($('#payment_code').val() != $(this).attr('payment_code')) {
            $('#payment_code').val($(this).attr('payment_code'));
            $(this).addClass('using');
        } else {
            $('#payment_code').val('');
        }

    });

    $('#next_button').on('click', function() {
        if ($('input[name="bean_pay"]').attr('checked') && $('#password_callback').val() != '1') {
            showDialog('使用金豆支付，需输入支付密码并确认  ', 'error', '', '', '', '', '', '', '', 2);
            return;
        }

        if ($('input[name="silver_pay"]').attr('checked') && $('#password_callback').val() != '1') {
            showDialog('使用银豆支付，需输入支付密码并确认  ', 'error', '', '', '', '', '', '', '', 2);
            return;
        }

        if ($('input[name="balance_pay"]').attr('checked') && $('#password_callback').val() != '1') {
            showDialog('使用余额支付，需输入支付密码并确认  ', 'error', '', '', '', '', '', '', '', 2);
            return;
        }

        if ($('#payment_code').val() == '' && parseFloat($('#api_pay_amount').html()) > 0) {
            showDialog('请选择一种在线支付方式', 'error', '', '', '', '', '', '', '', 2);
            return;
        }
        $('#buy_form').submit();
    });

    $('#bean_pay_submit').on('click', function() {
        if ($('#pay-password').val() == '') {
            showDialog('请输入支付密码', 'error', '', '', '', '', '', '', '', 2);
            return false;
        }
        $('#password_callback').val('');
        $.get("index.php?act=buy&op=check_pd_pwd", { 'password': $('#pay-password').val() }, function(data) {
            if (data == '1') {
                $('#password_callback').val('1');
                $('#bean_password').hide();
            } else {
                $('#pay-password').val('');
                showDialog('支付密码错误', 'error', '', '', '', '', '', '', '', 2);
            }
        });
    });

    $('input[type="checkbox"]').on('change', function() {
        var bean_type = $(this).attr("name");
        if (bean_type == 'bean_pay') {
            if ($('input[name="bean_pay"]').attr('checked')) {
                $('input[name="silver_pay"]').prop("checked", false);
                $('input[name="balance_pay"]').prop("checked", false);
                $('#pay-password').val('');
                $('#password_callback').val('');
                $('#bean_password').show();
                $('#payment_code').val('beanpay');
                $('.ncc-online-receipt-info').hide();
            } else {
                $('#bean_password').hide();
                $('#payment_code').val('');
                $('.ncc-online-receipt-info').show();
            }
        } else if (bean_type == 'silver_pay') {
            if ($('input[name="silver_pay"]').attr('checked')) {
                $('input[name="bean_pay"]').prop("checked", false);
                $('input[name="balance_pay"]').prop("checked", false);
                $('#pay-password').val('');
                $('#password_callback').val('');
                $('#bean_password').show();
                $('#payment_code').val('silverpay');
                $('.ncc-online-receipt-info').hide();
            } else {
                $('#bean_password').hide();
                $('#payment_code').val('');
                $('.ncc-online-receipt-info').show();
            }
        }else if (bean_type == 'balance_pay') {
            if ($('input[name="balance_pay"]').attr('checked')) {
                $('input[name="bean_pay"]').prop("checked", false);
                $('input[name="silver_pay"]').prop("checked", false);
                $('#pay-password').val('');
                $('#password_callback').val('');
                $('#bean_password').show();
                $('#payment_code').val('balancepay');
                $('.ncc-online-receipt-info').hide();
            } else {
                $('#bean_password').hide();
                $('#payment_code').val('');
                $('.ncc-online-receipt-info').show();
            }
        }
    });
});
</script>