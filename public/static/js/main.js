$(document).ready(function(){
    //reg
    var phoneFlag = 0;
    var codeFlag = 0;
    var waitTime=60;
    var passwdFlag =0;
    var lphoneFlag = 0;
    var lpassFlag = 0;

    $("#reg-phone").change(function(){
        if(!/^1[3-9]\d{9}$/.test($(this).val())){
            $(this).removeClass("is-valid").addClass("is-invalid").siblings(".invalid-feedback").text("请输入正确的手机号码");
            phoneFlag = 0;
        }else {
            $(this).removeClass("is-invalid").addClass("is-valid");
            phoneFlag = 1;
        }
    })
    $("#code").change(function(){
        $.ajax({
            type: "POST",
            url: "index/index/checkCode",
            data: {
                "code" : $("#code").val(),
            },
            success: data => {
                if(data){
                    $("#code").removeClass("is-invalid").addClass("is-valid");
                    codeFlag = 1;
                }else {
                    $("#code").removeClass("is-valid").addClass("is-invalid").siblings(".invalid-feedback").text("请输入正确的验证码");
                    codeFlag = 0;
                }
            },
            error: () => {
                alert("服务器错误")
            }
        })
    })
    $("#reg-license").click(()=>{
        if($("#reg-license").is(":checked")){
            $("#reg-license").removeClass("is-invalid").addClass("is-valid");
        }else{
            $("#reg-license").removeClass("is-valid").addClass("is-invalid");
        }
    })
    $("#reg-submit").click(function(){
        if(phoneFlag){
            $("#reg-phone").removeClass("is-invalid").addClass("is-valid");
        }else{
            $("#reg-phone").removeClass("is-valid").addClass("is-invalid").siblings(".invalid-feedback").text("请填写手机号");
            return false;
        }
        if(codeFlag){
            $("#code").removeClass("is-invalid").addClass("is-valid");
        }else{
            $("#code").removeClass("is-valid").addClass("is-invalid").siblings(".invalid-feedback").text("请输入验证码");
            return false;
        }
        
        if($("#reg-license").is(":checked")){
            $("#reg-license").removeClass("is-invalid").addClass("is-valid");
        }else{
            $("#reg-license").removeClass("is-valid").addClass("is-invalid");
            return false;
        }
        $.ajax({
            type: "POST",
            url: "index/index/register",
            data: {
                "phone" : $("#reg-phone").val(),
                "code" : $("#code").val()
            },
            success: data=> {
                if(data=="error"){
                    alert("服务器错误");
                }else if(data=="error1"){
                    $("#code").removeClass("is-valid").addClass("is-invalid").siblings(".invalid-feedback").text("验证码错误");
                }else if(data=="error2"){
                    $("#reg-phone").removeClass("is-valid").addClass("is-invalid").siblings(".invalid-feedback").text("请输入正确的手机号码");
                }else{
                    $("#phonenum").text(data);
                    time($("#resend"));
                    $("#reg-reset").click();
                    $("#code").removeClass("is-invalid is-valid");
                    $("#reg-phone").removeClass("is-invalid is-valid");
                    $("#reg-license").removeClass("is-invalid is-valid");
                }
            },
            error: ()=>{
                alert("服务器错误");
            }
        })
        $("#code-img").click();
    })

    $("#refresh").on({click:function(){
        $("#code-img").attr("src", "index/index/verify?id="+Math.random());
        $("#code").removeClass("is-invalid");
    },mouseover:function(){
        $(this).children().css({
            transform: "rotate(360deg)",
            transition: "all 500ms linear"
        })
    },mouseout:function(){
        $(this).children().css({
            transform: "rotate(0)",
            transition: "all 500ms linear"
        })
    }
    }).css("cursor","pointer")

    // reg2
    function time(ele) {
        if (waitTime == 0) {
            ele.removeAttr("disabled");
            ele.text("重新发送验证码");
            waitTime = 60;
        } else {
            ele.attr("disabled", "disabled")
            ele.text("重新发送验证码("+waitTime+"s)");
            waitTime--;
            setTimeout(function() {
                time(ele)
            }, 1000)
        }
    }

    $("#resend").click(function(){
        $.ajax({
            type: "POST",
            url: "index/index/sendCodeSms",
            success: ()=>{
                time($(this));
            },
            error: ()=>{
                alert("服务器错误");
            }
        })
    })
    //reg2  
    $("#reg-password").change(function(){
        var $p = $(this).val();
        if(($p.length>7&&$p.length<21)&&((/\d/.test($p)&&/[a-zA-Z]/.test($p))||(/[a-zA-Z]/.test($p)&&/\W/.test($p))||(/\d/.test($p)&&/\W/.test($p)))){
            $(this).removeClass("is-invalid").addClass("is-valid");
            passwdFlag = 1;
        }else{
            $(this).removeClass("is-valid").addClass("is-invalid").siblings(".invalid-feedback").text("密码强度不符合要求, 请输入8-20位字母/数字/符号,至少包含两种的密码");
            passwdFlag = 0;
        }
    })
    $("#reg-submit2").click(function(){
        if(passwdFlag){
            $("#reg-password").removeClass("is-invalid").addClass("is-valid");
        }else{
            $("#reg-password").removeClass("is-valid").addClass("is-invalid").siblings(".invalid-feedback").text("请输入用于注册的密码");
            return false;
        }
        $.ajax({
            type: "POST",
            url: "index/index/register2",
            data: {
                "smscode": $("#sms-code").val(),
                "password": $("#reg-password").val()
            },success: data=>{
                if(data=="error"){
                    alert("服务器错误")
                }else if(data=="error1"){
                    $("#reg-password").removeClass("is-valid").addClass("is-invalid").siblings(".invalid-feedback").text("密码强度不符合要求, 请输入8-20位字母/数字/符号,至少包含两种的密码");
                }else if(data=="error2"){
                        $("#sms-code").removeClass("is-valid").addClass("is-invalid").siblings(".invalid-feedback").text("验证码错误");
                }else{
                    alert("成功注册");
                    $("#reg2-reset").click();
                    $("#reg-password").removeClass("is-invalid is-valid");
                }
            },error: ()=>{
                alert("服务器错误");
            }
        })
    })

    // login
    $("#login-phone").change(function(){
        if(/^1[3-9]\d{9}$/.test($(this).val())){
            lphoneFlag = 1;
            $(this).removeClass("is-invalid")
            
        }else {
            lphoneFlag=0;
            $(this).addClass("is-invalid").removeClass("is-valid").siblings(".invalid-feedback").text("手机号格式错误");
        }
    })

    $("#login-password").on({focus:function(){
        $(this).removeClass("is-invalid");
    },change:function(){
        if($(this).val==0){
            $(this).addClass("is-invalid").removeClass("is-valid").siblings(".invalid-feedback").text("请输入密码");
            lpassFlag = 0;
        }else{
            $(this).removeClass("is-invalid");
            lpassFlag = 1;
        }
    }
    })
    $("#login").click(function(){
        if(lphoneFlag){
            $("#login-phone").addClass("is-valid").removeClass("is-invalid");
        }else{
            $("#login-phone").addClass("is-invalid").removeClass("is-valid").siblings(".invalid-feedback").text("请填写手机号");
            return false;
        }
        if(!lpassFlag){
            $("#login-password").addClass("is-invalid").removeClass("is-valid").siblings(".invalid-feedback").text("请输入密码");
            return false;
        }
        $.ajax({
            type: "POST",
            url: "index/index/login",
            data: {
                "phone" : $("#login-phone").val(),
                "password" : $("#login-password").val()
            },
            success: data=>{
                if(data=="success"){
                    $("#modal").modal("hide");
                    alert("登录成功");
                    $("#login-password").removeClass("is-invalid is-valid");
                    $("#login-phone").removeClass("is-valid is-invalid");
                    $("#login-reset").click();
                }else if(data=="error1"){
                    $("#login-phone").addClass("is-invalid").siblings(".invalid-feedback").text("此用户不存在");
                }else if(data=="error2"){
                    $("#login-password").addClass("is-invalid").siblings(".invalid-feedback").text("用户名或者手机号错误");
                }else{
                    alert("服务器错误");
                }
            },
            error: ()=>{
                alert("服务器错误")
            }
        })
        return false;
    })
})