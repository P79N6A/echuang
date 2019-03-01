<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3 class="left-top">红包推送时间设置</h3>
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
                        <label for="site_name">红包推送时间</label>
                    </dt>
                    <dd class="opt">
                        <input id="site_name" name="time_setting" value="<?php echo $output['result']['time_setting'];?>" class="input-txt1" type="text" />分
                        <p class="notic">用户红包推送时间</p>
                    </dd>
                </dl>
                <div class="bot">
                    <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">确认提交</a>
                </div>
            </div>

    </form>
</div>
