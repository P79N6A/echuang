<?php defined('In33hao') or exit('Invild Access!');?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=redbag_task&op=redbag_task" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>添加任务</h3>
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
            <li>添加任务信息</li>
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
                <dd class="opt">
                    <input id="title" name="title" value="" class="input-txt1" type="text" />
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">任务总数</label>
                </dt>
                <dd class="opt">
                    <input id="task_number" name="number" value="" class="input-txt1" type="text" />
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">任务内容</label>
                </dt>
                <dd class="opt">
                    <input id="quantity_received" name="content" value="" class="input-txt1" type="text" />
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">任务图片</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show"><span class="type-file-box">
                        <input type="text" name="picture[]" id="textfield1" class="type-file-text" />
                        <input type="button" name="button1" id="button1" multiple="multiple"  value="选择上传..." class="type-file-button" />
                        <input class="type-file-file" id="site_logo" name="picture[]" multiple="multiple"  type="file" size="30" >
                        </span>
                    </div>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">对比图片</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show"><span class="type-file-box">
                        <input type="text" name="contrast_picture" id="textfield2" class="type-file-text" />
                        <input type="button" name="button1" id="button2" multiple="multiple"  value="选择上传..." class="type-file-button" />
                        <input class="type-file-file" id="contrast_logo" name="contrast_picture" multiple="multiple"  type="file" size="30" >
                        </span>
                    </div>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">任务总金额</label>
                </dt>
                <dd class="opt">
                    <input id="task_number" name="total_sum" value="" class="input-txt1" type="text" />
                </dd>
            </dl>
            <div class="bot">
                <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">提交</a>
            </div>
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
        $('#time_zone').attr('value','<?php echo $output['list_setting']['time_zone'];?>');
    });
</script>
