<?php defined('In33hao') or exit('Access Invild!');?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>账户管理</h3>
                <h5>会员系统会员账户调整</h5>
            </div>
            <?php echo $output['top_link']; ?>
        </div>
    </div>
    <form id="adjust_form" method="post" name="form">
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
                    <label>账户类型</label>
                </dt>
                <dd class="opt">
                    <select id="account_type" name="account_type">
                        <option value="1" <?php echo ($output['account_type'] == '1') ? 'selected' : ''; ?>>积分</option>
                        <option value="2" <?php echo ($output['account_type'] == '2') ? 'selected' : ''; ?>>预期积分</option>
                    </select>
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>增减类型</label>
                </dt>
                <dd class="opt">
                    <select id="operate_type" name="operate_type">
                        <option value="1" <?php echo ($output['operate_type'] == '1') ? 'selected' : ''; ?>>增加</option>
                        <option value="2" <?php echo ($output['operate_type'] == '2') ? 'selected' : ''; ?>>减少</option>
                    </select>
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>数量</label>
                </dt>
                <dd class="opt">
                    <input type="text" id="adjust_amount" name="adjust_amount" class="input-txt" />
                    <span class="err"></span>
                    <p class="notic">对应数量填写</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>描述</label>
                </dt>
                <dd class="opt">
                    <textarea name="adjust_sdesc" rows="6" class="tarea"></textarea>
                    <span class="err"></span>
                    <p class="notic">描述信息将显示在明细相关页，会员和管理员都可见</p>
                </dd>
            </dl>
            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn">确认提交</a></div>
        </div>
    </form>
</div>
<script type="text/javascript">
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
    $.getJSON("index.php?act=account&op=check_member", { 'mobile': membermobile }, function(data) {
        if (data) {
            $("#tr_memberinfo").show();
            var msg = "会员" + data.name + "，当前积分:" + data.balance + "，当前预期积分:" + data.equity;
            $("#member_mobile").val(membermobile);
            $("#member_id").val(data.id);
            $("#td_memberinfo").text(msg);
        } else {
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
            adjust_amount: {
                required: true,
                min: 0.01
            }
        },
        messages: {
            member_mobile: {
                required: '<i class="fa fa-exclamation-circle"></i>请输入会员手机号'
            },
            member_id: {
                required: '<i class="fa fa-exclamation-circle"></i>会员信息错误，请重新填写会员名'
            },
            adjust_amount: {
                required: '<i class="fa fa-exclamation-circle"></i>请添加金额',
                min: '<i class="fa fa-exclamation-circle"></i>金额必须是数字且大于0'
            }
        }
    });
});
</script>