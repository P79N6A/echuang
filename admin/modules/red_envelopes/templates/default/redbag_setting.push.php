<?php defined('In33hao') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=advertisement&op=index" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>添加广告</h3>
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
                        <label><em>*</em>商家手机号码</label>
                    </dt>
                    <dd class="opt">
                        <input type="text" name="member_mobile" id="member_mobile" class="input-txt" onchange="javascript:checkmember();">
                        <span class="err"></span>
                        <p class="notic">请填写商家手机号码</p>
                    </dd>
                </dl>
                <dl class="row" id="tr_memberinfo">
                    <dt class="tit">符合条件的商家</dt>
                    <dd class="opt" id="td_memberinfo"></dd>
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
    function checkmember() {
        var membermobile = $.trim($("#member_mobile").val());
        if (membermobile == '') {
            $("#member_id").val('0');
            $("#tr_memberinfo").hide();
            return false;
        }
        $.getJSON("index.php?act=advertisement&op=check_member", { 'mobile': membermobile }, function(data) {
            if(data){
                $("#tr_memberinfo").show();
                var msg = "会员:" + data.name + "  店名:" + data.title;
                $("#member_mobile").val(membermobile);
                $("#member_id").val(data.id);
                $("#td_memberinfo").text(msg);
            }else {
                $("#member_mobile").val('');
                $("#member_id").val('0');
                $("#tr_memberinfo").hide();
                alert("会员信息错误");
            }
        });
    }

    $(function() {
        $("#tr_memberinfo").hide();
        $('#adjust_form').validate({
            errorPlacement: function(error, element) {
                var error_td = element.parent('dd').children('span.err');
                error_td.append(error);
            },
            rules: {
                member_mobile: {
                    required: true
                },
                member_id: {
                    required: true
                },
                title: {
                    required: true,
                },
                address: {
                    required: true,
                },
                mobile: {
                    required: true,
                },
                content: {
                    required: true,
                }
            },
            messages: {
                member_mobile: {
                    required: '<i class="fa fa-exclamation-circle"></i>请输入会员手机号'
                },
                member_id: {
                    required: '<i class="fa fa-exclamation-circle"></i>会员信息错误，请重新填写会员名'
                },
                title: {
                    required: '<i class="fa fa-exclamation-circle"></i>请输入商家名称'
                },
                address: {
                    required: '<i class="fa fa-exclamation-circle"></i>请输入商家地址'
                },
                mobile: {
                    required: '<i class="fa fa-exclamation-circle"></i>请输入商家电话'
                },
                content: {
                    required: '<i class="fa fa-exclamation-circle"></i>请输入商家内容'
                }
            }
        });
    });
</script> 

