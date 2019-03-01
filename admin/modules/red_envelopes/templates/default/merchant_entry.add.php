<?php defined('In33hao') or exit('Access Invild!');?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=merchant_entry&op=merchant_entry" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>添加商家</h3>
                <!-- <h5><?php echo $lang['member_shop_manage_subhead']; ?></h5> -->
            </div>
        </div>
    </div>
    <form id="adjust_form" enctype="multipart/form-data" method="post" name="form">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="member_id" id="member_id" value='0' />
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>会员手机号码</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="member_mobile" id="member_mobile" class="input-txt" onchange="javascript:checkmember();">
                    <span class="err"></span>
                    <p class="notic">请填写会员手机号码</p>
                </dd>
            </dl>
            <dl class="row" id="tr_memberinfo">
                <dt class="tit">符合条件的会员</dt>
                <dd class="opt" id="td_memberinfo"></dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>商店名称</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="title" id="title" class="input-txt">
                    <span class="err"></span>
                    <p class="notic">请填写商店名称</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>商店地址</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="address" id="address" class="input-txt">
                    <span class="err"></span>
                    <p class="notic">请填写商店地址</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>商店电话</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="mobile" id="mobile" class="input-txt">
                    <span class="err"></span>
                    <p class="notic">请填写商店电话</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>商店内容</label>
                </dt>
                <dd class="opt">
                    <textarea name="content" rows="6" class="tarea"></textarea>
                    <span class="err"></span>
                    <p class="notic">请填写商店内容</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>商店分类</label>
                </dt>
                <dd class="opt">
                    <select name="classify" id="classify">
                        <option value="">请选择</option>
                        <option value="1">衣</option>
                        <option value="2">食</option>
                        <option value="3">住</option>
                        <option value="4">行</option>
                    </select>
                    <span class="err"></span>
                    <p class="notic">请选择商店分类</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>营业执照</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show"><span class="type-file-box">
                        <input type="text" name="licence" id="textfield1" class="type-file-text" />
                        <input type="button" name="button1" id="button1" value="选择上传..." class="type-file-button" />
                        <input class="type-file-file" id="site_logo" name="licence" type="file" size="30" hidefocus="true" nc_type="change_member_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                        </span></div>
                    <span class="err"></span>
                    <p class="notic">请上传营业执照图片</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>门店照片</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show"><span class="type-file-box">
                        <input type="text" name="store" id="textfield2" class="type-file-text" />
                        <input type="button" name="button2" id="button2" value="选择上传..." class="type-file-button" />
                        <input class="type-file-file" id="member_logo" name="store" type="file" size="30" hidefocus="true" nc_type="change_seller_center_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                        </span></div>
                    <span class="err"></span>
                    <p class="notic">请上传门店照片</p>
                </dd>
            </dl>
            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn">确认提交</a></div>
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
    $("#submitBtn").click(function() {
        if ($('#adjust_form').valid()) {
            $('#adjust_form').submit();
        }
    });
    function checkmember() {
        var membermobile = $.trim($("#member_mobile").val());
        if (membermobile == '') {
            $("#member_id").val('0');
            $("#tr_memberinfo").hide();
            return false;
        }
        $.getJSON("index.php?act=merchant_entry&op=check_member", { 'mobile': membermobile }, function(data) {
            if (data=="401") {
                $("#member_mobile").val('');
                $("#member_id").val('0');
                $("#tr_memberinfo").hide();
                alert("会员已注册过商家");
            } else if(data.name){
                $("#tr_memberinfo").show();
                var msg = "会员" + data.name;
                $("#member_mobile").val(membermobile);
                $("#member_id").val(data.id);
                $("#td_memberinfo").text(msg);
            }else {
                $("#member_mobile").val('');
                $("#member_id").val('0');
                $("#tr_memberinfo").hide();
                alert("会员信息错误");
            }
        });
    }

    $(function() {
        $("#tr_memberinfo").hide();
        $('#adjust_form').validate({
            errorPlacement: function(error, element) {
                var error_td = element.parent('dd').children('span.err');
                error_td.append(error);
            },
            rules: {
                member_mobile: {
                    required: true
                },
                member_id: {
                    required: true
                },
                title: {
                    required: true,
                },
                address: {
                    required: true,
                },
                mobile: {
                    required: true,
                },
                content: {
                    required: true,
                }
            },
            messages: {
                member_mobile: {
                    required: '<i class="fa fa-exclamation-circle"></i>请输入会员手机号'
                },
                member_id: {
                    required: '<i class="fa fa-exclamation-circle"></i>会员信息错误，请重新填写会员名'
                },
                title: {
                    required: '<i class="fa fa-exclamation-circle"></i>请输入商家名称'
                },
                address: {
                    required: '<i class="fa fa-exclamation-circle"></i>请输入商家地址'
                },
                mobile: {
                    required: '<i class="fa fa-exclamation-circle"></i>请输入商家电话'
                },
                content: {
                    required: '<i class="fa fa-exclamation-circle"></i>请输入商家内容'
                }
            }
        });
    });
</script>