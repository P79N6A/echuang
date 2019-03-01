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
                <dt class="tit">设置城主是否开启</dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="site_status1" class="cb-enable <?php if($output['result']['auction']=='1'){ ?> selected <?php } ?>" >开启</label>
                        <label for="site_status0" class="cb-disable <?php if($output['result']['auction']=='0'){ ?>selected<?php } ?>" >关闭 </label>
                        <input id="site_status1" name="auction" value="1" type="radio">
                        <input id="site_status0" name="auction" value="0" type="radio">
                    </div>
                </dd>
            </dl>
            <div class="bot">
                <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">确认提交</a>
            </div>
        </div>
    </form>
</div>
