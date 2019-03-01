<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>基本设置</h3>
                <h5></h5>
            </div>
        </div>
    </div>
    <!-- 操作说明 -->
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title'];?>">平台设置</h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
        <ul>
            <li>平台设置参数设置</li>
        </ul>
    </div>
    <form method="post" enctype="multipart/form-data" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">平台运营状态</dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="site_status1" class="cb-enable <?php echo ($output['s_info']['site_status']==1)?'selected"':'';?>">开启</label>
                        <label for="site_status0" class="cb-disable <?php echo ($output['s_info']['site_status']==0)?'selected"':'';?>">关闭</label>
                        <input id="site_status1" name="site_status" <?php echo ($output['s_info']['site_status']==1)?'checked="checked"':'';?> value="1" type="radio">
                        <input id="site_status0" name="site_status" <?php echo ($output['s_info']['site_status']==0)?'checked="checked"':'';?> value="0" type="radio">
                    </div>
                    <p class="notic">可暂时将站点关闭，其他人无法访问，但不影响管理员访问后台</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="closed_reason">关闭原因</label>
                </dt>
                <dd class="opt">
                    <textarea name="closed_reason" rows="6" class="tarea" id="closed_reason"><?php echo $output['s_info']['closed_reason']?$output['s_info']['closed_reason']:'升级中……';?></textarea>
                    <p class="notic">当网站处于关闭状态时，关闭原因将显示在前台</p>
                </dd>
            </dl>

            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">确认提交</a></div>
        </div>
    </form>
</div>