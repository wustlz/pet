function order(beg_id){
	// 判断是否在登录状态
	var user_type = $('#user_type').val();
	if(user_type=='2'){
		//获取带"/"的项目名
		var pathName = window.document.location.pathname;
		var path = pathName.substring(0, pathName.substr(1).indexOf('/')+1);
		$.ajax({
			url : path + '/index.php/Home/Order/order', //此处是提交的地址 
			data : {
				beg_id : beg_id
			},  //此处是需要提交给后台的数据
			type : "post",
			cache : false,
			async: false,
			dataType : "text", // 返回json数据
			success : function(data) {
				if(data == '404'){
					//重新跳转到首页
					location.href = path;
				} else if(data == '0'){
					alert('Order Failed! The beg is in proccessing!');
				} else if(data == '1'){
					alert('Order Failed! The beg has been done!');
				} else if(data == '2'){
					alert('Order Failed! You should complete your profile in personal center!');
				} else if(data == '9'){
					alert('Order successfully!');
				} else if(data == '10'){
					alert('Network error, please refresh the page and try again');
				} else {
					alert('Order Failed!');
				}
			},
			error : function() {  
	           alert("Network error, please refresh the page and try again");
			}  
		});
	} else if(user_type=='1'){
		alert("You are an Owner, can't order begs");
		return ;
	} else {
		alert('Please Login first!');
		return ;
	}
}