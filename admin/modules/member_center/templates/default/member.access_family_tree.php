<?php defined('In33hao') or exit('Access Invild!');?>
<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_TEMPLATES_URL ?>/css/familytree.css">
<div class="page" style="width: 2000px; height: 500px;">
    <div class="fixed-bar">
        <div class="item-title">
            <a class="back" href="index.php?act=member&op=member_manage" title="返回会员列表">
                <i class="fa fa-arrow-circle-o-left"></i>
            </a>
            <div class="subject">
                <h3>接点人族谱图</h3>
                <h5>会员系统查看各个接点人关系图</h5>
            </div>
            <div class="title">
                <form method="post" action="index.php?act=member&op=access_family_tree" name="formSearch" id="formSearch">
                    <input type="hidden" name="form_submit" value="ok" />
                    <div class="sDiv">
                        <span>会员手机号</span>
                        <label class="sDiv2">
                            <input class="s-input-txt" value="<?php echo $output['mobile']; ?>" name="mobile" type="text"/>
                            <input type="button" class="search-btn" value="搜索" style="margin-left: 6px;" />
                            <input type="button" class="back-btn" value="返回" style="margin-left: 6px;" />
                        </label>
                    </div>
                </form>
            </div>
        </div>
        <div class="familytree">
            <?php if (!empty($output['tree'])) {?>
            <div class="tree">
                <?php echo $output['tree']; ?>
            </div>
            <?php } else {?>
            <div class="no-data">
                <span><i class="fa fa-exclamation-circle"></i>没有符合当前搜索的记录</span>
            </div>
            <?php }?>
        </div>
    </div>
</div>
<script type="text/javascript">
$(function() {
    $('.search-btn').click(function() {
        $('#formSearch').submit();
    });
    $('.back-btn').click(function() {
        window.history.back();
    });
})
</script>