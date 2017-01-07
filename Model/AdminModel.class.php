<?php
//管理员数据模型Model
namespace Model;
use Think\Model;

//父类Model  ThinkPHP/Library/Think/Model.class.php

class AdminModel extends Model{

	// 是否批处理验证
	protected $patchValidate = true;
	//实现表单验证
	//通过重写父类属性_validate实现表单验证
	protected $_validate = array(
		//验证用户名
		array('admin_name','require','Name is required'),
		array('admin_password','require','Password is required'),
	);

	//对姓名和密码进行验证
	function checkNamePwd($name, $password){
		// 根据$email查询是否有此记录
		$admin = M("Admin");
		$info = $admin -> where('admin_name="'.$name.'"') -> find();
		if($info != null){
			//验证密码
			if($info['admin_password'] != md5($password)){
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