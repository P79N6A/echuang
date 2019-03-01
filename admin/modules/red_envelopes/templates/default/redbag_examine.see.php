<?php defined('In33hao') or exit('Invild Access!');?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=redbag_issue&op=redbag_manage" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>查看红包审核信息</h3>
                <!-- <h5><?php echo $lang['member_shop_manage_subhead']; ?></h5> -->
            </div>
        </div>
    </div>
    <form id="bank_form" enctype="multipart/form-data" method="post" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="inserest_id" value="">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="member_mobile">红包单号</label>
                </dt>
                <dd class="opt">
                    <label><?php echo $output['info']['issue']['red_envelopes_order_number']; ?></label>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="member_mobile">用户手机</label>
                </dt>
                <dd class="opt">
                    <label><?php echo $output['info']['issue']['member_mobile']; ?></label>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="member_mobile">红包金额</label>
                </dt>
                <dd class="opt">
                    <label><?php echo $output['info']['issue']['money']; ?></label>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="member_mobile">红包个数</label>
                </dt>
                <dd class="opt">
                    <label><?php echo $output['info']['issue']['red_envelopes_num']; ?></label>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="member_mobile">定位范围</label>
                </dt>
                <dd class="opt">
                    <label><?php echo $output['info']['issue']['red_envelopes_province'].$output['info']['issue']['red_envelopes_city'].$output['info']['issue']['red_envelopes_area']; ?></label>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="member_mobile">年龄阶段</label>
                </dt>
                <dd class="opt">
                    <label><?php echo $output['info']['age_name']; ?></label>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="member_mobile">爱好</label>
                </dt>
                <dd class="opt">
                    <label><?php echo $output['info']['name']; ?></label>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="member_mobile">性别</label>
                </dt>
                <dd class="opt">
                    <label><?php echo $output['info']['gender']; ?></label>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="member_mobile">图片</label>
                </dt>
                <dd class="opt">
                    <?php if($output['info']['envelopes']){ ?>
                        <?php foreach($output['info']['envelopes'] as $k => $v){ ?>
                            <img width="27%" src="<?php echo  UPLOAD_SITE_URL.DS.ATTACH_MOBILE.DS.$v ?>">
                        <?php } ?>
                    <?php } ?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="member_mobile">广告词</label>
                </dt>
                <dd class="opt">
                    <label><?php echo $output['info']['issue']['red_envelopes_contentpicture']; ?></label>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="member_mobile">链接</label>
                </dt>
                <dd class="opt">
                    <label><?php echo $output['info']['issue']['link']; ?></label>
                </dd>
            </dl>
        </div>
    </form>
</div>
