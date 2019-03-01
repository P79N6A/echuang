<?php defined('In33hao') or exit('Access Invild!');?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>会员系统登陆</title>
	<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
	<link href="<?php echo MEMBER_SYSTEM_TEMPLATES_URL ?>/css/base.css" rel="stylesheet" type="text/css">
	<link href="<?php echo MEMBER_SYSTEM_TEMPLATES_URL ?>/css/seller_center.css" rel="stylesheet" type="text/css">
	<link href="<?php echo MEMBER_SYSTEM_RESOURCE_SITE_URL; ?>/font/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	<script language="JavaScript" type="text/javascript">
		// window.onload = function() {
		// 	tips = new Array(2);
		// 	tips[0] = document.getElementById("loginBG01");
		// 	tips[1] = document.getElementById("loginBG02");
		// 	index = Math.floor(Math.random() * tips.length);
		// 	tips[index].style.display = "block";
		// };
		$(document).ready(function() {
		// 更换验证码
		function change_seccode() {
		    $('#codeimage').attr('src', 'index.php?act=seccode&op=makecode&nchash=<?php echo $output['nchash']; ?>&t=' + Math.random());
		    $('#captcha').select();
		}

		$('[nctype="btn_change_seccode"]').on('click', function() {
		    change_seccode();
		});

		// 登陆表单验证
		$("#form_login").validate({
		    errorPlacement:function(error, element) {
		        element.prev(".repuired").append(error);
		    },
		    onkeyup: false,
		    rules:{
		        user_name: {
		            required:true
		        },
		        password: {
		            required:true
		        },
		        captcha: {
		            required:true,
		            remote:{
		                url:"index.php?act=seccode&op=check&nchash=<?php echo $output['nchash']; ?>",
		                type:"get",
		                data:{
		                    captcha:function() {
		                        return $("#captcha").val();
		                    }
		                },
		                complete: function(data) {
		                    if(data.responseText == 'false') {
		                        change_seccode();
		                    }
		                }
		            }
		        }
		    },
		    messages:{
		        user_name: {
		            required:"<i class='icon-exclamation-sign'></i>用户名不能为空"
		        },
		        password:{
		            required:"<i class='icon-exclamation-sign'></i>密码不能为空"
		        },
		        captcha:{
		            required:"<i class='icon-exclamation-sign'></i>验证码不能为空",
		            remote:"<i class='icon-frown'></i>验证码错误"
		        }
		    }
		});
		//Hide Show verification code
		$("#hide").click(function(){
		    $(".code").fadeOut("slow");
		});
		$("#captcha").focus(function(){
		    $(".code").fadeIn("fast");
		});

		});
	</script>
</head>
<body>
	<div id="loginBG01" class="ncsc-login-bg">
  		<p class="pngFix"></p>
	</div>
	<div class="ncsc-login-container">
  		<div class="ncsc-login-title">
    		<h2>会员系统中心</h2>
    		<!-- <span>使用入驻申请时填写的“商家用户名”作为登录用户名<br/>
    				登录密码则商城用户密码一致</span> -->
		</div>
  		<form id="form_login" action="" method="post" >
    		<?php Security::getToken();?>
    		<input name="nchash" type="hidden" value="<?php echo $output['nchash']; ?>" />
    		<input type="hidden" name="form_submit" value="ok" />
		    <div class="input">
		      	<label>用户名</label>
		      	<span class="repuired"></span>
		      	<input name="user_name" type="text" autocomplete="off" class="text" autofocus>
		      	<span class="ico"><i class="icon-user"></i></span> </div>
		    <div class="input">
		      	<label>密码</label>
		      	<span class="repuired"></span>
		      	<input name="password" type="password" autocomplete="off" class="text">
		      	<span class="ico"><i class="icon-key"></i></span> </div>
		    <div class="input">
		      	<label>验证码</label>
		      	<span class="repuired"></span>
		      	<input type="text" name="captcha" id="captcha" autocomplete="off" class="text" style="width: 80px;" maxlength="4" size="10" />
		      	<div class="code">
		        	<div class="arrow"></div>
		        	<div class="code-img">
		        		<a href="javascript:void(0)" nctype="btn_change_seccode"><img src="index.php?act=seccode&op=makecode&nchash=<?php echo $output['nchash']; ?>" name="codeimage" border="0" id="codeimage"></a>
		        	</div>
		        	<a href="JavaScript:void(0);" id="hide" class="close" title="<?php echo $lang['login_index_close_checkcode']; ?>"><i></i></a> <a href="JavaScript:void(0);" class="change" nctype="btn_change_seccode" title="<?php echo $lang['login_index_change_checkcode']; ?>"><i></i></a>
		        </div>
		      	<span class="ico"><i class="icon-qrcode"></i></span>
		      	<input type="submit" class="login-submit" value="确认登录" />
		    </div>
		    <div class="mt10 fr"><a href="index.php?act=login&op=register" title="注册账号">注册账号</a></div>
		</form>
	</div>
</body>
</html>