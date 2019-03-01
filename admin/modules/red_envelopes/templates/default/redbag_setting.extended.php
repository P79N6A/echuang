<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3 class="left-top">红包设置</h3>
            </div>
            <!--子程序菜单-->
                <?php echo $output['top_link'];?>
        </div>
    </div>
    <form method="post" enctype="multipart/form-data" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">扩大范围设置</dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="site_status1" class="cb-enable <?php if($output['result']['state']=='1'){ ?> selected <?php } ?>" >开启</label>
                        <label for="site_status0" class="cb-disable <?php if($output['result']['state']=='0'){ ?>selected<?php } ?>" >关闭 </label>
                        <input id="site_status1" name="state" value="1" type="radio">
                        <input id="site_status0" name="state" value="0" type="radio">
                    </div>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">推荐注册</label>
                </dt>
                <dd class="opt">
                    <input id="site_name" name="recommended_registration" value="<?php echo $output['result']['recommended_registration'] ?>" class="input-txt1" type="text" />人
                    <p class="notic">成功推荐下级人数</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">扩大定位范围</label>
                </dt>
                <dd class="opt">
                    <input id="site_name" name="add_red_envelopes_range" value="<?php echo $output['result']['add_red_envelopes_range'] ?>" class="input-txt1" type="text" />公里
                    <p class="notic">成功推荐下级注册后，红包定位范围扩大的公里数</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="icp_number">扩大定位范围增加红包数</label>
                </dt>
                <dd class="opt">
                    <input id="icp_number" name="add_red_envelopes_num" value="<?php echo $output['result']['add_red_envelopes_num'] ?>" class="input-txt1" type="text" />个
                    <p class="notic">每扩大以上公里，可增加的红包个数</p>
                </dd>
            </dl>
            <div class="bot">
                <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">确认提交</a>
            </div>
        </div>

    </form>
</div>
