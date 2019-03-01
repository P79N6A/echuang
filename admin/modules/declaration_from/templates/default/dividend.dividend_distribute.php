<?php defined('In33hao') or exit('Access Invild!');?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>分红分发</h3>
                <h5></h5>
            </div><?php echo $output['top_link']; ?>
        </div>
    </div>
    <!-- 操作说明 -->
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title']; ?>">分红分发</h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span']; ?>"></span> </div>
        <ul>
            <li>分红分发参数设置</li>
            <li>设置浮动分红数就可以分发分红</li>

        </ul>
    </div>
    <form method="post" enctype="multipart/form-data" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <div class="ncap-form-default system-setting">
            <dl class="row">
                <dt class="tit">
                    <label for="float_dividend_amount">浮动分红数：</label>
                </dt>
                <dd class="opt">
                    <input class="w100" id="float_dividend_amount" name="float_dividend_amount" value="0" class="input-txt" type="text">
                    <p class="notic">浮动分红=浮动分红数*金券数量</p>
                </dd>
            </dl>


            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">确认分红</a></div>
        </div>
    </form>
</div>