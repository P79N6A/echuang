<?php defined('In33hao') or exit('Invild Access!');?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=merchant_entry&op=index" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>更改商店信息</h3>
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
            <li>更改更改商店信息</li>
        </ul>
    </div>
    <form id="bank_form" enctype="multipart/form-data" method="post" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="age_id" value="">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">用户名称</label>
                </dt>
                <dd class="opt">
                    <?php echo $output['info']['member_name']?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">用户电话</label>
                </dt>
                <dd class="opt">
                    <?php echo $output['info']['member_mobile']?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">商店名称</label>
                </dt>
                <dd class="opt">
                    <input name="title" value="<?php echo $output['info']['title'] ?>" class="input-txt1" type="text" />
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">商店地址</label>
                </dt>
                <dd class="opt">
                    <input name="address" value="<?php echo $output['info']['address'] ?>" class="input-txt1" type="text" />
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">商店电话</label>
                </dt>
                <dd class="opt">
                    <input name="mobile" value="<?php echo $output['info']['mobile'] ?>" class="input-txt1" type="text" />
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">商店分类</label>
                </dt>
                <dd class="opt">
                    <?php echo $output['info']['classify']?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">商店内容</label>
                </dt>
                <dd class="opt">
                    <textarea name="content" rows="6" class="tarea"><?php echo $output['info']['content']?></textarea>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">营业执照</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show"><span class="show"><a class="nyroModal" rel="gal" href="<?php echo $output['info']['licence'] ?>"> <i class="fa fa-picture-o" onMouseOver="toolTip('<img src=<?php echo  $output['info']['licence']?>>')" onMouseOut="toolTip()"/></i> </a></span><span class="type-file-box">
                        <input type="text" name="licence" id="textfield1" class="type-file-text" />
                        <input type="button" name="licence" id="button1" value="选择上传..." class="type-file-button" />
                        <input class="type-file-file" id="site_logo" name="licence" type="file" size="30" hidefocus="true" nc_type="change_site_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                        </span></div>
                    <span class="err"></span>
                    <p class="notic">营业执照</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">门店照片</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show"><span class="show"><a class="nyroModal" rel="gal" href="<?php echo $output['info']['store'] ?>"> <i class="fa fa-picture-o" onMouseOver="toolTip('<img src=<?php echo  $output['info']['store']?>>')" onMouseOut="toolTip()"/></i> </a></span><span class="type-file-box">
                        <input type="text" name="store" id="textfield2" class="type-file-text" />
                        <input type="button" name="store" id="button1" value="选择上传..." class="type-file-button" />
                        <input class="type-file-file" id="member_logo" name="store" type="file" size="30" hidefocus="true" nc_type="change_member_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                        </span></div>
                    <span class="err"></span>
                    <p class="notic">门店照片</p>
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