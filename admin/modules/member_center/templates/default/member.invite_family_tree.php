<?php defined('In33hao') or exit('Access Invild!');?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL ?>/js/jquerytree/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL ?>/js/jquerytree/css/style.css" />
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL ?>/js/jquerytree/js/jquery-1.7.2.min.js"></script>
<div class="page" style="width: 1000px; height: 2000px;">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=member&op=member_manage" title="返回会员列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>邀请人族谱图</h3>
                <h5>会员系统查看各个邀请人关系图</h5>
            </div>
            <div class="title">
                <form method="post" action="index.php?act=member&op=invite_family_tree" name="formSearch" id="formSearch">
                    <input type="hidden" name="form_submit" value="ok" />
                    <div class="sDiv">
                        <span>会员手机号</span>
                        <label class="sDiv2">
                            <input class="s-input-txt" value="<?php echo $output['mobile']; ?>" name="mobile" type="text" />
                            <input type="button" class="search-btn" value="搜索" />
                            <input type="button" class="back-btn" value="返回">
                        </label>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="tree" id="ul_div" style="margin-top: 80px;">
        <?php if ($output['member_id']) {?>
        <ul>
            <li class="parent_li">
                <span id="<?php echo $output['member_id']; ?>" title="Expand this branch" onclick="ajax_data(this)"><i class="icon-plus-sign"></i><?php echo $output['member_mobile'] . '[' . $output['member_truename'] . ']'; ?></span>
            </li>
        </ul>
        <?php } else {?>
            <div class="no-data">
                <span><i class="fa fa-exclamation-circle"></i>没有符合当前搜索的记录</span>
            </div>
        <?php }?>
    </div>
</div>
<script type="application/javascript">
var arr = [];

function ajax_data(o) {
    // var url = "http://192.168.0.152:804/api/index.php?act=area&op=test";
    var init_val = $(o).attr("id");
    if (contains(arr, init_val)) {
        var children = $(o).parent('li.parent_li').find(' > ul > li');
        if (children.is(":visible")) {
            children.hide('fast');
            $(o).attr('title', 'Expand this branch').find(' > i').addClass('icon-plus-sign').removeClass('icon-minus-sign');
        } else {
            children.show('fast');
            $(o).attr('title', 'Collapse this branch').find(' > i').addClass('icon-minus-sign').removeClass('icon-plus-sign');
        }
    } else {
        var children = $(o).parent('li.parent_li').find(' > ul > li');
        if (children.is(":visible")) {
            children.hide('fast');
            $(o).attr('title', 'Expand this branch').find(' > i').addClass('icon-plus-sign').removeClass('icon-minus-sign');
        } else {
            children.show('fast');
            $(o).attr('title', 'Collapse this branch').find(' > i').addClass('icon-minus-sign').removeClass('icon-plus-sign');
        }
        var bb = Math.ceil(Math.random() * 100000);
        var aa = parseInt(init_val) + bb;
        $.getJSON("index.php?act=member&op=get_invite_list", { 'member_id': init_val }, function(json) {
            if (json.length == 0) {
                alert("下面没有推荐人了");
            } else {
                $("#" + init_val).after("<ul id='" + aa + "'></ul>");
                for (var i = 0; i < json.length; i++) {
                    $("#" + aa).append("<li class='parent_li'><span title='Collapse this branch' onclick='ajax_data(this)' id='" + json[i].member_id + "'><i class='icon-plus-sign'></i>" + json[i].member_mobile + '[' + json[i].member_truename + "]</span></li>");
                }
            }
        });
        arr.push(init_val);
    }
}

function contains(arr, obj) {
    var i = arr.length;
    while (i--) {
        if (arr[i] === obj) {
            return true;
        }
    }
    return false;
}

$(function() {
    $('.search-btn').click(function() {
        $('#formSearch').submit();
    });
    $('.back-btn').click(function() {
        window.history.go(-1);
    });
})
</script>