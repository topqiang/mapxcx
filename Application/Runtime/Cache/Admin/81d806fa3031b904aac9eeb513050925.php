<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>后台登陆</title>
    <link rel="stylesheet" href="/api/Public/Admin/css/bootstrap.min.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="/api/Public/Admin/css/toocms.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="/api/Public/Admin/css/login.css" type="text/css" media="screen" />
    <script type="text/javascript" src="/api/Public/Common/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="/api/Public/Admin/js/ajax-operate.js"></script>
    <script type="text/javascript" src="/api/Public/Admin/js/bootstrap.min.js"></script>
</head>
<body>
<div class="login-wrapper">
    <div class="col-md-4 col-md-offset-4 logo-page">
    </div>
    <div class="container" style="margin-top:5%">
        <div class="row">
            <div class="col-md-4 col-md-offset-4 box">
                <div class="content-wrap">
                    <h6>后台登陆</h6>
                    <p class="text-danger bg-danger text-alert  displaynone" id="login-error">账户或密码错误，请重新登录！</p>
                    <form method="post" action="<?php echo U('Index/index');?>" class="form">
                        <div class="form-group col-lg-12" id="account-form" >
                            <div class="input-group input-group-lg col-lg-12">
                                <span class="input-group-addon"><span aria-hidden="true" class="icon glyphicon glyphicon-user"></span></span>
                                <input class="form-control" name="account" id="account" type="text" placeholder="账号" onblur="check_account()">
                            </div>
                        </div>
                        <div class="form-group col-lg-12">
                            <div class="input-group input-group-lg col-lg-12">
                                <span class="input-group-addon"><span aria-hidden="true" class="icon glyphicon glyphicon-lock"></span></span>
                                <input class="form-control" name="password" type="password" onkeydown="if(event.keyCode==13){document.getElementById('loginButton').click();}" placeholder="密码">
                            </div>
                        </div>
                        <div class="form-group col-lg-12 hidden" id="verifyDiv">
                            <div class="input-group input-group-lg col-lg-6" style="float:left; margin-right:10px;">
                                <span class="input-group-addon"><span aria-hidden="true" class="icon glyphicon glyphicon-random"></span></span>
                                <input class="form-control text-input" name="verify" onkeydown="if(event.keyCode==13){document.getElementById('loginButton').click();}" type="text" placeholder="验证码">
                            </div>
                            <div class="fl">
                                <img src="<?php echo U('Manager/verify');?>" onclick="change_verify();" id="verify_img" width="145" height="45">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 login-btn">
                                <input type="hidden" name="errorNum" id="errorNum" value="0" />
                                <p><input type="button" id="loginButton" class="btn btn-primary btn-lg btn-block submit-btn" value="立刻登录"></p>
                            </div>
                        </div>
                        <div class="notification"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
    function change_verify(){
        var str = "<?php echo U('Manager/verify');?>";
        document.getElementById('verify_img').src=str+'/'+new Date().getTime();
    }

    function check_account(){
        var account = $('#account').val();
        var url = "<?php echo U('Manager/checkAccount');?>";
        if(account==""){
            $('.notification').html('请输入用户名');
        }else{
            $.ajax({
                url : url,
                type:'POST',
                data: {account:account},
                dataType:'JSON',
                success:function(data){
                    if(data.error !=null){
                        $('.notification').html(data.error);
                    }else{
                        document.getElementById("errorNum").value=data.success;
                        if(data.success>=3){
                            $('#verifyDiv').removeClass('hidden');
                        }
                    }
                }
            });
        }
    }

    $(function(){
        $('#account').focus();
        var dologin = "<?php echo U('Manager/doLogin');?>";
        var verify = "<?php echo U('Manager/verify');?>";
        $('.submit-btn').click(function(){
            if(document.getElementById("errorNum").value>=3){
                if($("input[name='verify']").val()==""){
                    $('.notification').html('请输入验证码');
                    $('#verifyDiv').removeClass('hidden');
                }else{
                    ajaxLogin(dologin,verify);
                }
            }else{
                ajaxLogin(dologin,verify);
            }

        });
    });
</script>
</html>