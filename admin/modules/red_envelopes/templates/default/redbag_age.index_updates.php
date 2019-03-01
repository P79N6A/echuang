<?php defined('In33hao') or exit('Invild Access!');?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=age&op=index" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>更改年龄段</h3>
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
            <li>更改年龄段信息</li>
        </ul>
    </div> 
    <form id="bank_form" enctype="multipart/form-data" method="post" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="age_id" value="">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">年龄阶段</label>
                </dt>
                <dd class="opt">
                    <input id="site_name" name="left_age" value="<?php echo $output['result']['left_age']?>" class="input-txt1" type="text" />岁到
                    <input id="site_name" name="right_age" value="<?php echo $output['result']['right_age']?>" class="input-txt1" type="text" />岁
                </dd>
            </dl>
            <div class="bot">
                <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">提交</a>
            </div>
        </div>
    </form>
</div>
