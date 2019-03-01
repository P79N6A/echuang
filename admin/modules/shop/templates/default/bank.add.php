<?php defined('In33hao') or exit('Invild Access!');?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=bank&op=index" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>添加银行信息</h3>
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
            <li>添加银行信息</li>
        </ul>
    </div>
    <form id="bank_form" enctype="multipart/form-data" method="post">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="bank_id" value="<?php echo $output['bank_info']['bank_id']; ?>">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="bank_name">银行名称：</label>
                </dt>
                <dd class="opt">
                    <input type="text" value="" name="bank_name" id="bank_name" class="input-txt">
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="bank_abbreviation">银行简称：</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="bank_abbreviation" id="bank_abbreviation" class="input-txt" value="">
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="bank_logo">银行图标：</label>
                </dt>
                <dd class="opt">
                    <input class="img-upload" type="file" id="_pic" name="_pic">
                    <img id="my_pic" src="<?php echo ADDPIC_SITE_URL; ?>" width="30" height="30"/>
                    <input type="hidden" name="bank_logo" id="bank_logo" value="" />
                    <p class="notic">银行图标请使用90*90像素的图片。</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="bank_state">银行状态：</label>
                </dt>
                <dd class="opt">
                    <label>
                        <input type="radio" checked value="1" name="bank_state"> 支持
                    </label>
                    <label style="margin-left: 30px;">
                        <input type="radio" value="0" name="bank_state"> 不支持
                    </label>
                    <p class="notic"></p>
                </dd>
            </dl>
            <div class="bot">
                <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn">
                    提交
                </a>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/ajaxfileupload/ajaxfileupload.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.Jcrop/jquery.Jcrop.js"></script>
<link href="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.Jcrop/jquery.Jcrop.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
$(function() {
    $('input[class="img-upload"]').change(uploadChange);

    function uploadChange() {
        var filepath = $(this).val();
        var extStart = filepath.lastIndexOf(".");
        var ext = filepath.substring(extStart, filepath.length).toUpperCase();
        if (ext != ".PNG" && ext != ".GIF" && ext != ".JPG" && ext != ".JPEG") {
            alert("文件格式不支持");
            $(this).attr('value', '');
            return false;
        }
        if ($(this).val() == '') return false;
        ajaxFileUpload();
    }

    function ajaxFileUpload() {
        $.ajaxFileUpload({
            url: '<?php echo ADMIN_SITE_URL; ?>/index.php?act=common&op=pic_upload&form_submit=ok&uploadpath=<?php echo BANK_AVATAR; ?>',
            secureuri: false,
            fileElementId: '_pic',
            dataType: 'json',
            success: function(data, status) {
                if (data.status == 1) {
                    // ajax_form('cutpic', '<?php echo $lang['
                    //     nc_cut ']; ?>', '<?php echo ADMIN_SITE_URL; ?>/index.php?act=common&op=pic_cut&type=member&x=120&y=120&resize=1&ratio=1&url=' + data.url, 690);
                    // $('img').src(data.url);
                    $('#bank_logo').val(data.url);
                    var element = document.getElementById('my_pic');
                    element.src = data.url;
                } else {
                    alert(data.msg);
                }
                $('input[class="img-upload"]').bind('change', uploadChange);
            },
            error: function(data, status, e) {
                alert('图片上传失败');
                $('input[class="img-upload"]').bind('change', uploadChange);
            }
        })
    };

    //按钮先执行验证再提交表单
    $("#submitBtn").click(function() {
        // if ($("#bank_form").valid()) {
        $("#bank_form").submit();
        // }
    });
    // $('#bank_form').validate({
    //     errorPlacement: function(error, element) {
    //         var error_td = element.parent('dd').children('span.err');
    //         error_td.append(error);
    //     },
    //     rules: {
    //         member_name: {
    //             required: true,
    //             minlength: 3,
    //             maxlength: 20,
    //             remote: {
    //                 url: 'index.php?act=member&op=ajax&branch=check_user_name',
    //                 type: 'get',
    //                 data: {
    //                     user_name: function() {
    //                         return $('#member_name').val();
    //                     },
    //                     member_id: ''
    //                 }
    //             }
    //         },
    //         member_passwd: {
    //             required: true,
    //             maxlength: 20,
    //             minlength: 6
    //         },
    //         member_email: {
    //             required: true,
    //             email: true,
    //             remote: {
    //                 url: 'index.php?act=member&op=ajax&branch=check_email',
    //                 type: 'get',
    //                 data: {
    //                     user_name: function() {
    //                         return $('#member_email').val();
    //                     },
    //                     member_id: '<?php echo $output['
    //                     member_array ']['
    //                     member_id ']; ?>'
    //                 }
    //             }
    //         },
    //         member_qq: {
    //             digits: true,
    //             minlength: 5,
    //             maxlength: 11
    //         }
    //     },
    //     messages: {
    // member_name: {
    //     required: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['
    //     member_add_name_null ']; ?>',
    //     maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['
    //     member_add_name_length ']; ?>',
    //     minlength: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['
    //     member_add_name_length ']; ?>',
    //     remote: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['
    //     member_add_name_exists ']; ?>'
    // },
    // member_passwd: {
    //     required: '<i class="fa fa-exclamation-circle"></i><?php echo '
    //     密码不能为空 '; ?>',
    //     maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['
    //     member_edit_password_tip ']; ?>',
    //     minlength: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['
    //     member_edit_password_tip ']; ?>'
    // },
    // member_email: {
    //     required: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['
    //     member_edit_email_null ']; ?>',
    //     email: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['
    //     member_edit_valid_email ']; ?>',
    //     remote: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['
    //     member_edit_email_exists ']; ?>'
    // },
    // member_qq: {
    //     digits: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['
    //     member_edit_qq_wrong ']; ?>',
    //     minlength: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['
    //     member_edit_qq_wrong ']; ?>',
    //     maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['
    //     member_edit_qq_wrong ']; ?>'
    // }
    // }
    // });
});
</script>