<?php
//管理员数据模型Model
namespace Model;
use Think\Model;

//父类Model  ThinkPHP/Library/Think/Model.class.php

class IndexModel extends Model{

	// 是否批处理验证
	protected $patchValidate = true;
	//实现表单验证
	//通过重写父类属性_validate实现表单验证
	protected $_validate = array(
		array('index_phone','require','Phone is required'),
		array('index_phone','1,12','Phone is too long','2','length'),
		array("index_about",'require','About format is incorrect'),
		array('index_about','1,2000','About is too long','2','length'),

		array('index_title1','require','Title1 is required'),
		array('index_text1','require','Text1 is required'),
		array('index_title2','require','Title2 is required'),
		array('index_text2','require','Text2 is required'),
		array('index_title3','require','Title3 is required'),
		array('index_text3','require','Text3 is required'),
		array('index_title4','require','Title4 is required'),
		array('index_text4','require','Text4 is required'),
		array('index_title5','require','Title5 is required'),
		array('index_text5','require','Text5 is required'),
		array('index_title6','require','Title6 is required'),
		array('index_text6','require','Text6 is required'),

		array('index_title1','1,40','Title1 is too long','2','length'),
		array('index_text1','1,500','Text1 is too long','2','length'),
		array('index_title2','1,40','Title2 is too long','2','length'),
		array('index_text2','1,500','Text2 is too long','2','length'),
		array('index_title3','1,40','Title3 is too long','2','length'),
		array('index_text3','1,500','Text3 is too long','2','length'),
		array('index_title4','1,40','Title4 is too long','2','length'),
		array('index_text4','1,500','Text4 is too long','2','length'),
		array('index_title5','1,40','Title5 is too long','2','length'),
		array('index_text5','1,500','Text5 is too long','2','length'),
		array('index_title6','1,40','Title6 is too long','2','length'),
		array('index_text6','1,500','Text6 is too long','2','length'),

	);
}
?>