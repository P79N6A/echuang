<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3 class="left-top">分润设置</h3>
            </div>
            <!--子程序菜单-->
            <?php echo $output['top_link'];?>
        </div>
    </div>
    <form method="post" enctype="multipart/form-data" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">城主分润设置</dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="site_status1" class="cb-enable <?php if($output['result']['state']=='1'){ ?> selected <?php } ?>" >开启</label>
                        <label for="site_status0" class="cb-disable <?php if($output['result']['state']=='0'){ ?> selected <?php } ?>" >关闭 </label>
                        <input id="site_status1" name="state" value="1" type="radio">
                        <input id="site_status0" name="state" value="0" type="radio">
                    </div>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">区域内用户发红包</label>
                </dt>
                <dd class="opt">
                    <input id="site_name" name="member_hair" value="<?php echo $output['result']['member_hair']?>" class="input-txt1" type="text" />%
                    <p class="notic">区域内用户发红包，可获得红包总额的百分比</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">区域内用户抢红包</label>
                </dt>
                <dd class="opt">
                    <input id="site_name" name="member_rob" value="<?php echo $output['result']['member_rob']?>" class="input-txt1" type="text" />%
                    <p class="notic">区域内用户抢红包，可获得红包总额的百分比</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">现城给原城主返利设置</label>
                </dt>
                <dd class="opt">
                    <input id="site_name" name="city_rebate" value="<?php echo $output['result']['city_rebate']?>" class="input-txt1" type="text" />%
                    <p class="notic">现城给原城主返利百分比</p>
                </dd>
            </dl>
            <div class="bot">
                <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">确认提交</a>
            </div>
        </div>

    </form>
</div>
