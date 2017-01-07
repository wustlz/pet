<?php
//管理员数据模型Model
namespace Model;
use Think\Model;

//父类Model  ThinkPHP/Library/Think/Model.class.php

class ContactModel extends Model{

	// 是否批处理验证
	protected $patchValidate = true;
	//实现表单验证
	//通过重写父类属性_validate实现表单验证
	protected $_validate = array(
		array('contact_name','require','Name is required'),
		//验证邮箱
		array('contact_email','require','E-mail is required'),
		array("contact_email",'email','E-mail format is incorrect'),

		array('contact_subject','require','Subject is required'),
		array('contact_content','require','Message is required'),
	);
}