<?php defined('In33hao') or exit('Access Invalid!');?>
<?php if($item_edit_flag) { ?>

<div class="explanation" id="explanation">
  <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
    <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
  </div>
  <ul>
    <li>鼠标移动到内容上出现编辑按钮可以对内容进行修改</li>
    <li>操作完成后点击保存编辑按钮进行保存</li>
  </ul>
</div>
<?php } ?>
<div class="index_block home1">
  <?php if($item_edit_flag) { ?>
  <h3>模型版块布局A</h3>
  <?php } ?>
  <div class="title">
    <?php if($item_edit_flag) { ?>
    <h5>标题2：</h5>
    <input id="home1_title" type="text" class="txt w200" name="item_data[title]" value="<?php echo $item_data['title'];?>">
        <div><img id="titleImg" src="<?php echo  $output['item_info']['item_data']['textfield'] ?>"></div>
        <div class="input-file-show"><span class="type-file-box">
            <input type='text' value="<?php echo  $output['item_info']['item_data']['img_name']?>" name='textfield' id='textfield' class='type-file-text' />
            <input type='button' name='button' id='button' value='选择上传...' class='type-file-button' />
            <input id="btn_upload_image" type="file" name="special_image" class="type-file-file" size="30" hidefocus="true" >
            </span> </div>
        <div class="" style="float: right;color: #3b639f;font-size: 12px;margin-right: 33%;padding: 6.5px;background-color: #ddd;">
          <a nctype="btn_del_item_image1" href="javascript:;">删除</a>
        </div>
        <p style="color:#cdcdcd">建议标题图片尺寸：W1200px H110px</p>
    <?php } else { ?>
    <span><?php echo $item_data['title'];?></span>
    <?php } ?>
  </div>
  <div nctype="item_content" class="content">
    <?php if($item_edit_flag) { ?>
    <h5>内容：</h5>
    <?php } ?>
    <div nctype="item_image" class="item"> <img nctype="image" src="<?php echo getMbSpecialImageUrl($item_data['image']);?>" alt="">
      <?php if($item_edit_flag) { ?>
      <input nctype="image_name" name="item_data[image]" type="hidden" value="<?php echo $item_data['image'];?>">
      <input nctype="image_type" name="item_data[type]" type="hidden" value="<?php echo $item_data['type'];?>">
      <input nctype="image_data" name="item_data[data]" type="hidden" value="<?php echo $item_data['data'];?>">
      <a nctype="btn_edit_item_image" data-desc="640*260" href="javascript:;"><i class="fa fa-pencil-square-o"></i>编辑</a>
      <?php } ?>
    </div>
  </div>
</div>
<script type="text/javascript">
    $(function(){

        $("#sssss").click(function(){
            console.log($("#fupPhoto").val())
        })
    })
</script>