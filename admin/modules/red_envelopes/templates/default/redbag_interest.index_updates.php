<?php defined('In33hao') or exit('Invild Access!');?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=interest&op=index" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>更新红包爱好信息</h3>
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
            <li>更新红包爱好信息</li>
        </ul>
    </div>
    <form id="bank_form" enctype="multipart/form-data" method="post" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="inserest_id" value="">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">红包爱好</label>
                </dt>
                <dd class="opt">
                    <input id="site_name" name="name" value="<?php echo $output['result']['name']?>" class="input-txt1" type="text" />
                </dd>
            </dl>
            <div class="bot">
                <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">提交
                </a>
            </div>
        </div>
    </form>
</div>
