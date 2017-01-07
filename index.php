<?php

header('Content-Type:text/html; charset=utf-8');

//制作一个输出调试函数
function show_bug($msg){
	echo "<br /><pre>";
	var_dump($msg);
	echo "</pre><br />";
}


//定义css、images、js路径常量
define("SITE_URL","http://127.0.0.1/pet/");
define("CSS_URL",SITE_URL."Public/Home/css/");
define("IMG_URL",SITE_URL."Public/Home/images/");
define("JS_URL",SITE_URL."Public/Home/js/");

define("ADMIN_CSS_URL",SITE_URL."Public/Admin/stylesheets/");
define("ADMIN_IMG_URL",SITE_URL."Public/Admin/images/");
define("ADMIN_JS_URL",SITE_URL."Public/Admin/lib/");

// 为上传图片设置一个路径
define("IMG_UPLOAD",SITE_URL."Public/Upload/");

//把目前tp模式由生成模式变为开发模式
define("APP_DEBUG",true);
//引入框架的核心程序
include "./ThinkPHP/ThinkPHP.php";
?>