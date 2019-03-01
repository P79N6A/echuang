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
                    <dt class="tit">
                        <label for="site_name">红包定位范围</label>
                    </dt>
                    <dd class="opt">
                        <input id="site_name" name="red_envelopes_location_range" value="<?php echo $output['result']['red_envelopes_location_range'];?>" class="input-txt1" type="text" />公里
                        <p class="notic">用户定位可查看自己周边红包的公里数</p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label for="icp_number">定位范围可视红包数量</label>
                    </dt>
                    <dd class="opt">
                        <input id="icp_number" name="red_envelopes_location_num" value="<?php echo $output['result']['red_envelopes_location_num'] ?>" class="input-txt1" type="text" />个
                        <p class="notic">用户定位范围内可看见红包数量</p>
                    </dd>
                </dl>
                <div class="bot">
                    <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">确认提交</a>
                </div>
            </div>

    </form>
</div>
