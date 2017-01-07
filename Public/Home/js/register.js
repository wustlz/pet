$(document).ready(function(){

	$("#user_register").click(function(){

		var user_type = $("input[name='user_type']");
		var user_firstname = $("input[name='user_firstname']");
		var user_lastname = $("input[name='user_lastname']");
		var user_email = $("input[name='user_email']");
	    var user_password = $("input[name='user_password']");
	    var user_password2 = $("input[name='user_password2']");
	    var user_phone = $("input[name='user_phone']");
	    var user_location = $("input[name='user_location']");

		// 获取页面填写的内容
		if (user_type.val() == '') {
			alert("Please select your role");
			user_type.focus();
			return false;
		} else if (user_firstname.val() == '') {
			alert("Please input your First Name");
			user_firstname.focus();
			return false;
		} else if (user_lastname.val() == '') {
			alert("Please input your Last Name");
			user_lastname.focus();
			return false;
		} else if (user_email.val() == '') {
			alert("Please input your email");
			user_email.focus();
			return false;
		} else if (!checkEmail(user_email.val())){
			alert("Please input the correct email");
			user_email.focus();
			return false;
		} else if (user_password.val() == '') {
			alert("Please input password");
			user_password.focus();
			return false;
		} else if (user_password.val() != user_password2.val()) {
			alert("Repeat password must be same with the former password");
			user_password2.focus();
			return false;
		} else {
			return true;
		}
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