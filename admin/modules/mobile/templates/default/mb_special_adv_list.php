<?php defined('In33hao') or exit('Access Invalid!');?>
<div class="explanation" id="explanation">
<!--    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>-->
<!--        <h4 title="--><?php //echo $lang['nc_prompts_title'];?><!--">--><?php //echo $lang['nc_prompts'];?><!--</h4>-->
<!--    </div>-->
    <ul>
        <li>点击添加新的轮播条按钮可以添加新的轮播条</li>
        <li>鼠标移动到已有的轮播条上点击出现的删除按钮可以删除对应的轮播条</li>
        <li>操作完成后点击保存编辑按钮进行保存</li>
    </ul>
</div>
<div class="page">
    <!-- 页面导航 -->
    <div class="fixed-bar">
        <div class="item-title">
            <?php if($output['item_info']['special_id'] > 0) { ?>
                <a id="btn_back" href="<?php echo urlAdminMobile('mb_special', 'special_edit', array('special_id' => $output['item_info']['special_id']));?>" class="back"  title="返回上一级"><i class="fa fa-arrow-circle-o-left"></i></a>
            <?php } else { ?>
                <a id="btn_back" href="<?php echo urlAdminMobile('mb_special', 'index_edit');?>" class="back" title="返回上一级"><i class="fa fa-arrow-circle-o-left"></i></a>
            <?php } ?>
            <div class="subject">
                <h3><?php echo $output['item_title'];?></h3>
                <h5>手机客户端首页/专题页模板设置</h5>
            </div>
        </div>
    </div>
</div>
<span style="font-size: large">轮播图</span>
<hr>
<!--    --><?php //} ?>
    <div nctype="item_content" class="content">
<!--        --><?php //if($item_edit_flag) { ?>
            <h5>内容：</h5>
<!--        --><?php //} ?>
        <?php if(!empty($item_data['item']) && is_array($item_data['item'])) {?>
            <?php foreach($item_data['item'] as $item_key => $item_value) {?>
                <div nctype="item_image" class="item"> <img nctype="image" src="<?php echo getMbSpecialImageUrl($item_value['image']);?>" alt="">
                    <?php if($item_edit_flag) { ?>
                        <input nctype="image_name" name="item_data[item][<?php echo $item_key;?>][image]" type="hidden" value="<?php echo $item_value['image'];?>">
                        <input nctype="image_type" name="item_data[item][<?php echo $item_key;?>][type]" type="hidden" value="<?php echo $item_value['type'];?>">
                        <input nctype="image_data" name="item_data[item][<?php echo $item_key;?>][data]" type="hidden" value="<?php echo $item_value['data'];?>">
                        <a nctype="btn_del_item_image" href="javascript:;"><i class="fa fa-trash-o
"></i>删除</a>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
<!--    --><?php //if($item_edit_flag) { ?>
        <a nctype="btn_add_item_image" class="ncap-btn" data-desc="640*340" href="javascript:;"><i class="fa fa-plus"></i>添加新的图片</a>
<!--    --><?php //} ?>
</div>
