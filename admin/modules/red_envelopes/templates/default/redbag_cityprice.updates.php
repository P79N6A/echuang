<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=city_price&op=index" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>修改竞价城主原价</h3>
                <!-- <h5><?php echo $lang['member_shop_manage_subhead']; ?></h5> -->
            </div>
        </div>
    </div>
    <form method="post" enctype="multipart/form-data" name="form1">
            <div class="ncap-form-default">
                <dl class="row">
                    <dt class="tit">
                        <label for="login_pic1">省市区: </label>
                    </dt>
                    <dd class="opt">
                        <input id="site_name" value="<?php echo $output['list_setting']['province_name']?>" class="input-txt1" type="text" readonly="readonly"/>
                        <input id="site_name" value="<?php echo $output['list_setting']['city_name']?>" class="input-txt1" type="text" readonly="readonly"/>
                        <input id="site_name" value="<?php echo $output['list_setting']['area_name']?>" class="input-txt1" type="text" readonly="readonly"/>
                    </dd>
                </dl>
             <dl class="row">
                 <dt class="tit">
                     <label for="site_name">城主原价</label>
                 </dt>
                 <dd class="opt">
                     <input id="site_name" name="price" value="<?php echo $output['list_setting']['price']?>" class="input-txt1" type="text" />
                 </dd>
             </dl>
            <div class="bot">
                <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">确认提交</a>
            </div>
        </div>

    </form>
</div>
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL;?>/js/jquery.nyroModal.js"></script> 

