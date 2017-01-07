<?php
//管理员数据模型Model
namespace Model;
use Think\Model;

//父类Model  ThinkPHP/Library/Think/Model.class.php

class UserLoginModel extends Model{

	// 是否批处理验证
	protected $patchValidate = true;
	//实现表单验证
	//通过重写父类属性_validate实现表单验证
	protected $_validate = array(
		//验证用户名
		array('user_firstname','require','First Name is required'),
		array('user_lastname','require','Last Name is required'),

		// 验证手机号
		// 都是数字，长度11，首位不为0
		// 正则验证 /[1-9]\d{10}$/
		array("user_phone",'/^[1-9]\d{10}$/','Phone number is incorrect',2),
		// 验证用户类型
		array('user_type',array(1,2),'Your role is required',1,'in'),
		//验证邮箱
		array('user_email','require','E-mail is required'),
		// 验证邮箱
		array("user_email",'email','E-mail format is incorrect'),
		// 验证邮箱唯一
		array('user_email','','The E-mail has been registered',0,'unique',1),
		//验证密码
		array('user_password','require','Password is required'),
		array('user_password2','user_password','Repeat password must be same with the former password',0,'confirm'),
	);


	//对邮箱和密码进行验证
	function checkEmailPwd($email, $password){
		// 根据$email查询是否有此记录
		$UserLogin = M("User_login");
		$info = $UserLogin -> where('user_email="'.$email.'"') -> find();
		if($info != null){
			//验证密码
			if($info['user_password'] != md5($password)){
				return false;
			} else {
				return $info;
			}
		} else {
			return false;
		}
	}
}
?>