$(document).ready(function(){
	$("#login").click(function(){
		// 获取页面填写的内容
		var email = $("#email").val();
		var password = $("#password").val();
		var remember_user = $("#checkbox").val();
		if (email == '') {
			alert("Please input email");
			$("#email").focus();
		} else if (password == '') {
			alert("Please input password");
			$("#password").focus();
		} else if (!checkEmail(email)){
			alert("Please input the correct email");
			$("#email").focus();
		} else {
			//获取带"/"的项目名
			var pathName = window.document.location.pathname;
			var path = pathName.substring(0, pathName.substr(1).indexOf('/')+1);
			$.ajax({
				url : path + '/index.php/Home/User/login', //此处是提交的地址 
				data : {
					user_email : email,
					user_password : password,
					remember_user : remember_user
				},  //此处是需要提交给后台的数据
				type : "post",
				cache : false,
				async: false,
				dataType : "json", // 返回json数据
				success : function(data) {
					if(data.status == 1){
						//重新跳转到首页
						location.href = path;
					} else {
						alert(data.error);
						$("#password").val('');
					}

				},
				error : function() {  
		           alert("Network error, please refresh the page and log in again");
				}  
			});
		}
	});
	//控制a标签上颜色变化
	$("#index_exit").hover(function() {
        $(this).css('color','#ECC30A');
    }, function() {
        $(this).css('color','#ffffff');
    });
});

// check email format
function checkEmail(email){
    if (!/(^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$)/.test(email)) {
        return false;
    } else {
    	return true;
    }
}