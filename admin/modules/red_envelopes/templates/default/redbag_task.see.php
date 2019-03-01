<?php defined('In33hao') or exit('Invild Access!');?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=redbag_task&op=redbag_task" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>查看任务</h3>
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
            <li>查看任务信息</li>
        </ul>
    </div>
    <form id="bank_form" enctype="multipart/form-data" method="post" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="age_id" value="">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">任务标题</label>
                </dt>
                <dd class="opt"><?php echo $output['result']['task_title']?></dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">任务总数</label>
                </dt>
                <dd class="opt"><?php echo $output['result']['task_number']?></dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">任务内容</label>
                </dt>
                <dd class="opt"><?php echo $output['result']['task_content']?></dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">任务图片</label>
                </dt>
                <dd class="opt">
                    <?php foreach ($output['result']['task_picture'] as $k=>$v){ ?>
                        <img src="<?php echo $v; ?>" style="width: 100px;">
                    <?php } ?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">对比图片</label>
                </dt>
                <dd class="opt">
                    <img src="<?php echo $output['result']['task_contrast_picture']?>" style="width: 100px;">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">任务总金额</label>
                </dt>
                <dd class="opt"><?php echo $output['result']['task_total_sum']?></dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">任务订单号</label>
                </dt>
                <dd class="opt"><?php echo $output['result']['task_order']?></dd>
            </dl>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(function() {
        $("#site_logo").change(function () {
            $("#textfield1").val($(this).val());
        });
        $("#contrast_logo").change(function () {
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
        $('#time_zone').attr('value','<?php echo $output['result']['time_zone'];?>');
    });
</script>
