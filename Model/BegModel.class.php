<?php
//管理员数据模型Model
namespace Model;
use Think\Model;

//父类Model  ThinkPHP/Library/Think/Model.class.php

class BegModel extends Model{

	// 是否批处理验证
	protected $patchValidate = true;
	//实现表单验证
	//通过重写父类属性_validate实现表单验证
	protected $_validate = array(
		//验证邮箱
		array('beg_email','require','E-mail is required'),
		array("beg_email",'email','E-mail format is incorrect'),
		array('beg_title','require','Abstract is required'),

		// 验证手机号
		// 都是数字，长度11，首位不为0
		// 正则验证 /[1-9]\d{10}$/
		array('beg_phone','require','Phone number is required'),
		array("beg_phone",'/^[1-9]\d{10}$/','Phone number is incorrect',2),

		//验证地址
		array('beg_location','require','Location is required'),

		// 验证pet
		array('pet_ids','check_pet_ids',"Pet's count must be more than 1",3,'callback'),
	);

	// 自定义方法验证pet_ids
	function check_pet_ids($pet_ids){
		if(count($pet_ids)<1){
			return false;
		} else {
			return true;
		}
	}
}