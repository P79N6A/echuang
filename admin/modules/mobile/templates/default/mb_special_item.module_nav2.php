<?php defined('Entrance') or exit('Access Invalid!');?>
<?php if ($item_edit_flag) {?>

    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title']; ?>"><?php echo $lang['nc_prompts']; ?></h4>
        </div>
        <ul>
            <li>鼠标移动到内容上出现编辑按钮可以对内容进行修改</li>
            <li>操作完成后点击保存编辑按钮进行保存</li>
        </ul>
    </div>
<?php }?>
<div class="index_block nav1">
    <?php if ($item_edit_flag) {?>
        <h3>首页导航列表2</h3>
    <?php }?>
    <div class="title">
        <?php if ($item_edit_flag) {?>
<!--            <h5>标题：</h5>-->
<!--            <input id="home1_title" type="text" class="txt w200" name="item_data[title]" value="--><?php //echo $item_data['title']; ?><!--">-->
<!--            <input type="file" name="imageFile" ID="fupPhoto"/>-->
        <?php } else {?>
            <span><?php echo $item_data['title']; ?></span>
        <?php }?>
    </div>
    <div class="content">
        <?php if ($item_edit_flag) {?>
            <h5>内容：</h5>
        <?php }?>
        <div class="nav1_2">
            <div nctype="item_image" class="item"> <img nctype="image" src="<?php echo getMbSpecialImageUrl($item_data['square_image'][0]); ?>" alt="">
                <?php if ($item_edit_flag) {?>
                    <input nctype="image_name" name="item_data[square_image][]" type="hidden" value="<?php echo $item_data['square_image'][0]; ?>">
                    <input nctype="image_type" name="item_data[square_type][]" type="hidden" value="<?php echo $item_data['square_type'][0]; ?>">
                    <input nctype="image_data" name="item_data[square_data][]" type="hidden" value="<?php echo $item_data['square_data'][0]; ?>">
                    <input nctype="image_title" name="item_data[square_title][]" type="hidden" value="<?php echo $item_data['square_title'][0]; ?>">
                    <a nctype="btn_edit_item_image" data-desc="48*48" href="javascript:;"><i class="fa fa-pencil-square-o"></i>编辑</a>
                <?php }?>
            </div>
            <div nctype="item_image" class="item"> <img nctype="image" src="<?php echo getMbSpecialImageUrl($item_data['square_image'][1]); ?>" alt="">
                <?php if ($item_edit_flag) {?>
                    <input nctype="image_name" name="item_data[square_image][]" type="hidden" value="<?php echo $item_data['square_image'][1]; ?>">
                    <input nctype="image_type" name="item_data[square_type][]" type="hidden" value="<?php echo $item_data['square_type'][1]; ?>">
                    <input nctype="image_data" name="item_data[square_data][]" type="hidden" value="<?php echo $item_data['square_data'][1]; ?>">
                    <input nctype="image_title" name="item_data[square_title][]" type="hidden" value="<?php echo $item_data['square_title'][1]; ?>">
                    <a nctype="btn_edit_item_image" data-desc="48*48" href="javascript:;"><i class="fa fa-pencil-square-o"></i>编辑</a>
                <?php }?>
            </div>
            <div nctype="item_image" class="item"> <img nctype="image" src="<?php echo getMbSpecialImageUrl($item_data['square_image'][2]); ?>" alt="">
                <?php if ($item_edit_flag) {?>
                    <input nctype="image_name" name="item_data[square_image][]" type="hidden" value="<?php echo $item_data['square_image'][2]; ?>">
                    <input nctype="image_type" name="item_data[square_type][]" type="hidden" value="<?php echo $item_data['square_type'][2]; ?>">
                    <input nctype="image_data" name="item_data[square_data][]" type="hidden" value="<?php echo $item_data['square_data'][2]; ?>">
                    <input nctype="image_title" name="item_data[square_title][]" type="hidden" value="<?php echo $item_data['square_title'][2]; ?>">
                    <a nctype="btn_edit_item_image" data-desc="48*48" href="javascript:;"><i class="fa fa-pencil-square-o"></i>编辑</a>
                <?php }?>
            </div>
            <div nctype="item_image" class="item"> <img nctype="image" src="<?php echo getMbSpecialImageUrl($item_data['square_image'][3]); ?>" alt="">
                <?php if ($item_edit_flag) {?>
                    <input nctype="image_name" name="item_data[square_image][]" type="hidden" value="<?php echo $item_data['square_image'][3]; ?>">
                    <input nctype="image_type" name="item_data[square_type][]" type="hidden" value="<?php echo $item_data['square_type'][3]; ?>">
                    <input nctype="image_data" name="item_data[square_data][]" type="hidden" value="<?php echo $item_data['square_data'][3]; ?>">
                    <input nctype="image_title" name="item_data[square_title][]" type="hidden" value="<?php echo $item_data['square_title'][3]; ?>">
                    <a nctype="btn_edit_item_image" data-desc="48*48" href="javascript:;"><i class="fa fa-pencil-square-o"></i>编辑</a>
                <?php }?>
            </div>
        </div>
        <div class="nav1_2">
            <div nctype="item_image" class="item"> <img nctype="image" src="<?php echo getMbSpecialImageUrl($item_data['square_image'][4]); ?>" alt="">
                <?php if ($item_edit_flag) {?>
                    <input nctype="image_name" name="item_data[square_image][]" type="hidden" value="<?php echo $item_data['square_image'][4]; ?>">
                    <input nctype="image_type" name="item_data[square_type][]" type="hidden" value="<?php echo $item_data['square_type'][4]; ?>">
                    <input nctype="image_data" name="item_data[square_data][]" type="hidden" value="<?php echo $item_data['square_data'][4]; ?>">
                    <input nctype="image_title" name="item_data[square_title][]" type="hidden" value="<?php echo $item_data['square_title'][4]; ?>">
                    <a nctype="btn_edit_item_image" data-desc="48*48" href="javascript:;"><i class="fa fa-pencil-square-o"></i>编辑</a>
                <?php }?>
            </div>
            <div nctype="item_image" class="item"> <img nctype="image" src="<?php echo getMbSpecialImageUrl($item_data['square_image'][5]); ?>" alt="">
                <?php if ($item_edit_flag) {?>
                    <input nctype="image_name" name="item_data[square_image][]" type="hidden" value="<?php echo $item_data['square_image'][5]; ?>">
                    <input nctype="image_type" name="item_data[square_type][]" type="hidden" value="<?php echo $item_data['square_type'][5]; ?>">
                    <input nctype="image_data" name="item_data[square_data][]" type="hidden" value="<?php echo $item_data['square_data'][5]; ?>">
                    <input nctype="image_title" name="item_data[square_title][]" type="hidden" value="<?php echo $item_data['square_title'][5]; ?>">
                    <a nctype="btn_edit_item_image" data-desc="48*48" href="javascript:;"><i class="fa fa-pencil-square-o"></i>编辑</a>
                <?php }?>
            </div>
            <div nctype="item_image" class="item"> <img nctype="image" src="<?php echo getMbSpecialImageUrl($item_data['square_image'][6]); ?>" alt="">
                <?php if ($item_edit_flag) {?>
                    <input nctype="image_name" name="item_data[square_image][]" type="hidden" value="<?php echo $item_data['square_image'][6]; ?>">
                    <input nctype="image_type" name="item_data[square_type][]" type="hidden" value="<?php echo $item_data['square_type'][6]; ?>">
                    <input nctype="image_data" name="item_data[square_data][]" type="hidden" value="<?php echo $item_data['square_data'][6]; ?>">
                    <input nctype="image_title" name="item_data[square_title][]" type="hidden" value="<?php echo $item_data['square_title'][6]; ?>">
                    <a nctype="btn_edit_item_image" data-desc="48*48" href="javascript:;"><i class="fa fa-pencil-square-o"></i>编辑</a>
                <?php }?>
            </div>
            <div nctype="item_image" class="item"> <img nctype="image" src="<?php echo getMbSpecialImageUrl($item_data['square_image'][7]); ?>" alt="">
                <?php if ($item_edit_flag) {?>
                    <input nctype="image_name" name="item_data[square_image][]" type="hidden" value="<?php echo $item_data['square_image'][7]; ?>">
                    <input nctype="image_type" name="item_data[square_type][]" type="hidden" value="<?php echo $item_data['square_type'][7]; ?>">
                    <input nctype="image_data" name="item_data[square_data][]" type="hidden" value="<?php echo $item_data['square_data'][7]; ?>">
                    <input nctype="image_title" name="item_data[square_title][]" type="hidden" value="<?php echo $item_data['square_title'][7]; ?>">
                    <a nctype="btn_edit_item_image" data-desc="48*48" href="javascript:;"><i class="fa fa-pencil-square-o"></i>编辑</a>
                <?php }?>
            </div>
        </div>
    </div>
</div>
