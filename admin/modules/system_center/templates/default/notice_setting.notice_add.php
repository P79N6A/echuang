<?php defined('In33hao') or exit('Access Invild!');?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>新增公告</h3>
                <h5>公告管理</h5>
            </div>
        </div>
    </div>
    <form id="notice_form" method="post" name="noticeForm">
        <input type="hidden" name="form_submit" value="ok" />
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="notice_title"><em>*</em>公告标题</label>
                </dt>
                <dd class="opt">
                    <input type="text" value="" name="notice_title" id="notice_title" class="input-txt">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="nc_id"><em>*</em>公告类型</label>
                </dt>
                <dd class="opt">
                    <select name="nc_id" id="nc_id">
                        <option value="">选择公告类型...</option>
                        <?php foreach ($output['nc_arr'] as $k => $v): ?>
                        <option value="<?php echo $k; ?>">
                            <?php echo $v; ?>
                        </option>
                        <?php endforeach?>
                    </select>
                    <span class="err"></span>
                    <p class="notic">选择公告类型</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>是否显示</label>
                </dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="notice_show1" class="cb-enable selected">
                            <?php echo $lang['nc_yes']; ?>
                        </label>
                        <label for="notice_show0" class="cb-disable">
                            <?php echo $lang['nc_no']; ?>
                        </label>
                        <input id="notice_show1" name="notice_show" checked="checked" value="1" type="radio">
                        <input id="notice_show0" name="notice_show" value="0" type="radio">
                    </div>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">排序</dt>
                <dd class="opt">
                    <input type="text" value="255" name="notice_sort" id="notice_sort" class="input-txt">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>公告内容</label>
                </dt>
                <dd class="opt">
                    <?php showEditor('notice_content');?>
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn">确认提交</a></div>
        </div>
    </form>
</div>
<script type="text/javascript">
// 按钮先执行验证再提交表单
$(function() {
    $("#submitBtn").click(function() {
        if ($("#notice_form").valid()) {
            $("#notice_form").submit();
        }
    });
});

$(document).ready(function() {
    $('#notice_form').validate({
        errorPlacement: function(error, element) {
            var error_td = element.parent('dd').children('span.err');
            error_td.append(error);
        },
        rules: {
            notice_title: {
                required: true
            },
            nc_id: {
                required: true
            },
            notice_content: {
                required: true
            },
            notice_sort: {
                number: true
            },
        },
        messages: {
            notice_title: {
                required: '<i class="fa fa-exclamation-circle"></i>公告标题不能为空'
            },
            nc_id: {
                required: '<i class="fa fa-exclamation-circle"></i>请选择公告类型'
            },
            notice_content: {
                required: '<i class="fa fa-exclamation-circle"></i>公告內容不能为空'
            },
            notice_sort: {
                number: '<i class="fa fa-exclamation-circle"></i>公告排序仅能为数字'
            },
        }
    });
});
</script>