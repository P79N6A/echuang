<?php defined('In33hao') or exit('Invild Access!');?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=business_securities&op=index" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>查看商家优恵劵</h3>
                <!-- <h5><?php echo $lang['member_shop_manage_subhead']; ?></h5> -->
            </div>
        </div>
    </div>
    <!-- 操作说明 -->
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title']; ?>"><?php echo $lang['nc_prompts']; ?></h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span']; ?>"></span> </div>
        <ul>
            <li>查看商家优恵劵</li>
        </ul>
    </div>
    <form id="bank_form" enctype="multipart/form-data" method="post" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="age_id" value="">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">商家名称</label>
                </dt>
                <dd class="opt">
                    <?php echo $output['info']['member_name']?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">商家电话</label>
                </dt>
                <dd class="opt">
                    <?php echo $output['info']['member_mobile']?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">商家兑换码</label>
                </dt>
                <dd class="opt">
                    <?php echo $output['info']['red_redeem_code']?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">商家标题</label>
                </dt>
                <dd class="opt">
                    <?php echo $output['info']['red_title']?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">商家地址</label>
                </dt>
                <dd class="opt">
                    <?php echo $output['info']['red_address']?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">商家折扣</label>
                </dt>
                <dd class="opt">
                    <?php echo $output['info']['red_discount']?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">商家数量</label>
                </dt>
                <dd class="opt">
                    <?php echo $output['info']['num']?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">商家已抢数量</label>
                </dt>
                <dd class="opt">
                    <?php echo $output['info']['already_issued']?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">用户己兑</label>
                </dt>
                <dd class="opt">
                    <?php echo $output['info']['swap'] ?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">商户添加时间</label>
                </dt>
                <dd class="opt">
                    <?php echo $output['info']['addtime'] ?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">优恵劵过期时间</label>
                </dt>
                <dd class="opt">
                    <?php echo $output['info']['ter_of_validity'] ?>
                </dd>
            </dl>
            <div class="bot">
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(function() {
        $("#site_logo").change(function () {
            $("#textfield1").val($(this).val());
        });
        $("#member_logo").change(function(){
            $("#textfield2").val($(this).val());
        });
        // 上传图片类型
        $('input[class="type-file-file"]').change(function(){
            var filepath=$(this).val();
            var extStart=filepath.lastIndexOf(".");
            var ext=filepath.substring(extStart,filepath.length).toUpperCase();
            if(ext!=".PNG"&&ext!=".GIF"&&ext!=".JPG"&&ext!=".JPEG"){
                alert("<?php echo $lang['default_img_wrong'];?>");
                $(this).attr('value','');
                return false;
            }
        });
// 点击查看图片
        $('.nyroModal').nyroModal();
        $('#time_zone').attr('value','<?php echo $output['list_setting']['time_zone'];?>');
    });
</script>