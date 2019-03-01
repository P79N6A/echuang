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
                <dt class="tit">会员分润设置</dt>
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
                    <label for="site_name">下级发红包</label>
                </dt>
                <dd class="opt">
                    <input id="site_name" name="recommender_giving_red_packets" value="<?php echo $output['result']['recommender_giving_red_packets']?>" class="input-txt1" type="text" />%
                    <p class="notic">下级发红包，可获得红包总额的百分比</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">下级抢红包</label>
                </dt>
                <dd class="opt">
                    <input id="site_name" name="grab_a_red_envelope" value="<?php echo $output['result']['grab_a_red_envelope']?>" class="input-txt1" type="text" />%
                    <p class="notic">下级抢红包，可获得红包总额的百分比</p>
                </dd>
            </dl>
            <div class="bot">
                <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">确认提交</a>
            </div>
        </div>

    </form>
</div>
