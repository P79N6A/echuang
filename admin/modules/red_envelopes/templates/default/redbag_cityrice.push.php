<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=city_price&op=index" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>添加竞价城主原价</h3>
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
                    <select name="province" id="sheng">
                        <option value="">请选择</option>
                    </select>
                    <select name="city" id="shi">
                        <option value="">请选择</option>
                    </select>
                    <select name="area" id="qu">
                        <option value="">请选择</option>
                    </select>
                </dd>
            </dl>
             <dl class="row">
                 <dt class="tit">
                     <label for="site_name">城主原价</label>
                 </dt>
                 <dd class="opt">
                     <input id="site_name" name="price" value="" class="input-txt1" type="text" />
                 </dd>
             </dl>
            <div class="bot">
                <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">确认提交</a>
            </div>
        </div>

    </form>
</div>
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL;?>/js/jquery.nyroModal.js"></script>

<script type="text/javascript">
    $(function(){
//获取省的信息
        ajaxFun(0,"sheng");
    });
    function ajaxFun(id,type){
        $.ajax({
            url:"index.php?act=advertisement&op=sjld",
            data:{id:id},//发送的数据
            success:function(data){//执行成功的回调
                strToArr(data,type);//调用函数
            }
        });
    }
    function strToArr(str,type){//字符串转数组
        var arr = str.split('^'),//第一维
            brr = [];//定义第二维数组
        for(var i=0;i<arr.length;i++){//循环遍历第一维的数组
            var temp =arr[i].split(',');//第二维
            brr.push(temp);//将第一维的数组放入第二维
        }
        addHTML(brr,type);//网页追加
    }
    function addHTML(brr,type){//网页追加
        var str ='<option value="">请选择</option>';
        for(var i in brr){
        str+='<option value="'+brr[i][0]+'">'+brr[i][1]+'</option>';
        }
        $('#'+type).html(str);
        }
        $('#sheng').change(function(){//省的下拉 值改变的时候添加事件
            var id=$(this).val();//获取选择的省的id
            ajaxFun(id,"shi");//ajax再获取市

        });
        $('#shi').change(function(){//市的下拉 值改变的时候添加事件
            var id=$(this).val();//获取选择的市的id
            ajaxFun(id,"qu");//ajax再获取区

        });
</script> 

