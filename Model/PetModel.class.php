<?php
//管理员数据模型Model
namespace Model;
use Think\Model;

//父类Model  ThinkPHP/Library/Think/Model.class.php

class PetModel extends Model{

	// 是否批处理验证
	protected $patchValidate = true;
	//实现表单验证
	//通过重写父类属性_validate实现表单验证
	protected $_validate = array(
		array('pet_name','require',"Pet's name is required"),
		array('pet_breed','require',"Pet's breed is required"),
		array('pet_birth','require',"Pet's birth is required"),
		// array('pet_small_photo','require',"Pet's photo is required"),
		// array('pet_big_photo','require',"Pet's photo is required"),
		array('pet_about','require',"Pet's about is required"),
		array('pet_feed','require',"Pet's feed is required"),
		array('pet_have','check_pet_have',"Pet gets on well with something must be more than 1",3,'callback'),
		array('pet_provide','check_pet_provide',"Pet would like something must be more than 1",3,'callback'),
		array('pet_helpTime',array(0,1,2,3),"When pet need help must be more than 1",3,'callback'),

	);

	// 自定义方法验证pet_have
	function check_pet_have($pet_have){
		if(count($pet_have)<1){
			return false;
		} else {
			return true;
		}
	}
	// 自定义方法验证pet_provide
	function check_pet_provide($pet_provide){
		if(count($pet_provide)<1){
			return false;
		} else {
			return true;
		}
	}
	// 自定义方法验证pet_helpTime
	function check_pet_helpTime($pet_helpTime){
		if(count($pet_helpTime)<1){
			return false;
		} else {
			return true;
		}
	}
}