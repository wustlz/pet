$(document).ready(function(){
	// 编辑个人信息内容
	$("#upt_personalInfo").click(function(){
		//将phone和location改为可修改样式
		var old_phone = $("#tab_personalInfo").find("tr").eq(2).find("td").eq(0).text();
		var old_location = $("#tab_personalInfo").find("tr").eq(3).find("td").eq(0).text();
		// 将对应位置修改为input
		var td_input_phone = "<input type='text' name='user_phone' value='" + old_phone + "' />";
		var td_input_localtion = "<input type='text' name='user_location' value='" + old_location + "' />";
		var td_input_submit = "<span class='code_btn_upt'>" +
							"<input type='button' onclick='upt_personInfo()' value='submit'>" + "</span>";
		$("#tab_personalInfo").find("tr").eq(2).find("td").eq(0).html(td_input_phone);
		$("#tab_personalInfo").find("tr").eq(3).find("td").eq(0).html(td_input_localtion+td_input_submit);
		// 按钮失效
		$("#upt_personalInfo").attr("disabled", true);
		// $("#submit-button").removeAttr("disabled");
	});

});

// 修改个人信息
function upt_personInfo(){
	// 获取input里面的内容
	var user_phone = $("input[name=user_phone]").val();
	var user_location = $("input[name=user_location]").val();
	// alert(user_phone);
	// alert(user_location);
	var pathName = window.document.location.pathname;
	var path = pathName.substring(0, pathName.substr(1).indexOf('/')+1);
	$.ajax({
		url : path + '/index.php/Home/User/upt_personInfo', //此处是提交的地址 
		data : {
			user_phone : user_phone,
			user_location : user_location
		},  //此处是需要提交给后台的数据
		type : "post",
		cache : false,
		async: false,
		dataType : "text", // 返回text数据
		success : function(data) {
			if(data == '1'){
				//重新跳转到个人中心
				location.href = path + '/index.php/Home/User/userCenter';
			} else {
				alert('update fail');
			}
		},
		error : function() {  
           alert("Network error, please refresh the page and log in again");
		}  
	});
}

// 修改个人头像
function upt_avatar(){
	$('.user_avatar').hide();
	$('#upt_avatar_html').show();
}
// 取消修改
function upt_cancel(){
	var pathName = window.document.location.pathname;
	var path = pathName.substring(0, pathName.substr(1).indexOf('/')+1);
	location.href = path + '/index.php/Home/User/userCenter';
}

// 修改个人profile
function upt_profile(){
	$('.user_profile').attr('readonly',false);
	$('.user_profile').css('border','1px solid #DCDCDC');
	$('#upt_profile').hide();
	$('#sub_profile').show();
	$('#cancel_profile').show();
}
function sub_profile(){
	//获取textarea中的内容
	var borrower_profile = $('.user_profile').val();
	// alert(borrower_profile);
	if(borrower_profile==''){
		alert('Please input your profile');
	} else {
		var pathName = window.document.location.pathname;
		var path = pathName.substring(0, pathName.substr(1).indexOf('/')+1);
		$.ajax({
			url : path + '/index.php/Home/Borrower/uptBorrowerProfile', //此处是提交的地址 
			data : {
				borrower_profile : borrower_profile
			},  //此处是需要提交给后台的数据
			type : "post",
			cache : false,
			async: false,
			dataType : "text", // 返回text数据
			success : function(data) {
				if(data == '1'){
					//重新跳转到个人中心
					location.href = path + '/index.php/Home/User/userCenter';
				} else {
					alert('update fail');
				}
			},
			error : function() {  
	           alert("Network error, please refresh the page and log in again");
			}  
		});
	}
}

// 完成任务确认
function completeOrder(order_id){
	var pathName = window.document.location.pathname;
	var path = pathName.substring(0, pathName.substr(1).indexOf('/')+1);
	$.ajax({
		url : path + '/index.php/Home/Order/completeOrder', //此处是提交的地址 
		data : {
			order_id : order_id
		},  //此处是需要提交给后台的数据
		type : "post",
		cache : false,
		async: false,
		dataType : "text", // 返回text数据
		success : function(data) {
			if(data == '1'){
				// 重新加载个人中心
				alert('Change status successfully');
				location.href = path + '/index.php/Home/User/userCenter';
			} else {
				alert('Network error, please refresh the page and try again');
			}
		},
		error : function() {  
           alert("Network error, please refresh the page and try again");
		}  
	});
}