function logined(){
    $.ajax({
        type: "POST",
        url: $("#login-btn").attr("data-purl"),
        success: data=>{
            if(data=="logged"){
                $("#login-btn").attr("onclick", "window.location.href='"+$("#login-btn").attr("data-url")+"'").removeAttr("data-toggle").html("<i class='fa fa-user-o' aria-hidden='true'></i>")
            }else{
                $("#login-btn").removeAttr("onclick").attr('data-togle', 'modal').html("登录")
            };
        }
    })
}