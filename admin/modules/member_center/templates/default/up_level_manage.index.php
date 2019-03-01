<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>收益分配</h3>
                <h5></h5>
            </div>
        </div>
    </div>
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>高级设置</h3>
                <h5>会员系统高级设置</h5>
            </div><?php echo $output['top_link']; ?>
        </div>
    </div>
    <form method="post" enctype="multipart/form-data" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <div style="margin-left: 10%;margin-right: 10%" class="ncap-form-default system-setting">

            <!--            <b style="font-size: small;">VIP</b>-->
            <dl class="row">
                <dt class="tit">
                    <label for="report_product_price"><b style="font-size: small;">VIP</b></label>　　　　
                </dt>
                <dd class="opt">
                    报单满&nbsp;&nbsp;<input class="w65" id="report_product_price" name="report_product_price" value="<?php echo $output['member_system_setting'][0]['report_product_price'] ? $output['member_system_setting'][0]['report_product_price'] : 1; ?>" class="input-txt" type="text">&nbsp;元
                    <p class="notic">购买报单产品，体验用户即升级成为VIP</p>
                </dd>
            </dl>
            <hr>
            <!--            <b style="font-size: small;">店主</b>-->
            <dl class="row">
                <dt class="tit">
                    <label for="shopkeeper "><b style="font-size: small;">店主</b></label>　　　　
                </dt>
                <dd class="opt">
                    加盟费：<input class="w100" id="shopkeeper_direct_vip_num" name="shopkeeper_direct_vip_num" value="<?php echo $output['s_info'][0]['ml_direct_vip_num'] ? $output['s_info'][0]['ml_direct_vip_num']: 3; ?>" class="input-txt" type="text">元；<br>
                    或团队VIP  <input class="w100" id="shopkeeper_direct_vip_num" name="shopkeeper_direct_vip_num" value="<?php echo $output['s_info'][0]['ml_direct_vip_num'] ? $output['s_info'][0]['ml_direct_vip_num']: 3; ?>" class="input-txt" type="text">&nbsp;人
                    ，且直推VIP  <input class="w100" id="shopkeeper_team_vip_num" name="shopkeeper_team_vip_num" value="<?php echo $output['s_info'][0]['ml_team_vip_num'] ? $output['s_info'][0]['ml_team_vip_num'] : 10; ?>" class="input-txt" type="text">&nbsp;人

                    <p class="notic">团队VIP人数和直推VIP人数达到此处限制，即可升级为店主</p>
                </dd>
            </dl>
            <hr>
            <!--            <b style="font-size: small;">合伙人</b>-->
            <dl class="row">
                <dt class="tit">
                    <label for="partner"><b style="font-size: small;">合伙人</b></label>　　　　
                </dt>
                <dd class="opt">
                    加盟费：<input class="w100" id="shopkeeper_direct_vip_num" name="shopkeeper_direct_vip_num" value="<?php echo $output['s_info'][0]['ml_direct_vip_num'] ? $output['s_info'][0]['ml_direct_vip_num']: 3; ?>" class="input-txt" type="text">元；<br>
                    团队店主  <input class="w100" id="partner_direct_vip_num" name="partner_direct_vip_num" value="<?php echo $output['s_info'][1]['ml_direct_vip_num'] ? $output['s_info'][1]['ml_direct_vip_num']: 3; ?>" class="input-txt" type="text">&nbsp;人
                    ，且直推店主  <input class="w100" id="partner_team_vip_num" name="partner_team_vip_num" value="<?php echo $output['s_info'][1]['ml_team_vip_num'] ? $output['s_info'][1]['ml_team_vip_num'] : 10; ?>" class="input-txt" type="text">&nbsp;人
                    <p class="notic">团队店主人数和直推店主人数达到此处限制，即可升级为合伙人</p>
                </dd>
            </dl>
            <hr>
            <!--            <b style="font-size: small;">高级合伙人</b>-->
            <dl class="row">
                <dt class="tit">
                    <label for="senior_partner"><b style="font-size: small;">高级合伙人</b></label>　　　　
                </dt>
                <dd class="opt">
                    加盟费：<input class="w100" id="shopkeeper_direct_vip_num" name="shopkeeper_direct_vip_num" value="<?php echo $output['s_info'][0]['ml_direct_vip_num'] ? $output['s_info'][0]['ml_direct_vip_num']: 3; ?>" class="input-txt" type="text">元；<br>
                    团队合伙人  <input class="w100" id="senior_partner_direct_vip_num" name="senior_partner_direct_vip_num" value="<?php echo $output['s_info'][2]['ml_direct_vip_num'] ? $output['s_info'][2]['ml_direct_vip_num'] : 3; ?>" class="input-txt" type="text">&nbsp;人
                    ，且直推合伙人  <input class="w100" id="senior_partner_team_vip_num" name="senior_partner_team_vip_num" value="<?php echo $output['s_info'][2]['ml_team_vip_num'] ? $output['s_info'][2]['ml_team_vip_num'] : 10; ?>" class="input-txt" type="text">&nbsp;人
                    <p class="notic">团队合伙人人数和直推合伙人人数达到此处限制，即可升级为高级合伙人</p>
                </dd>
            </dl>
            <hr>
            <dl class="row">
                <dt class="tit">
                    <label for="strategic_partner"><b style="font-size: small;">战略合伙人</b></label>　　　　
                </dt>
                <dd class="opt">
                    直推高级合伙人  <input class="w100" id="strategic_partner_direct_vip_num" name="strategic_partner_direct_vip_num" value="<?php echo $output['s_info'][3]['ml_direct_vip_num'] ? $output['s_info'][3]['ml_direct_vip_num'] : 3; ?>" class="input-txt" type="text">&nbsp;人
                    <p class="notic">直推高级合伙人人数达到此处限制，即可升级为战略合伙人</p>
                </dd>
            </dl>
            <hr>
            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">确认提交</a></div>
        </div>
    </form>
</div>