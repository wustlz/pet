<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        // 获取公共信息
        R('PublicInfo/headerFooter');
    	// 首页其他部分信息
        $this -> assign('index_info', $this->getIndex());
        // 首页loving persons
        $this -> getPersons();
        //调用视图display()
        $this->display();
    }

    // 读取首页数据库内容
    function getIndex(){
    	$index_model = M("index");
    	return $index_model -> where('index_id=1') -> find();
    }

    // 从数据库读取Loving persons
    function getPersons(){
    	$borrowerModel = M('Borrower');
        $sql = 'SELECT * FROM pet_borrower AS b, pet_user AS u WHERE b.user_id = u.user_id LIMIT 0,6';
    	$borrowers = $borrowerModel->query($sql);
        $size = count($borrowers);
        for($i=0; $i<$size; $i++){
            $borrower_profile = preg_replace('/[　|\s|\n|\r|\t]+/', ' ', $borrowers[$i]['borrower_profile'].trim());
            if(strlen($borrower_profile)>150){
                $borrowers[$i]['borrower_profile'] = substr($borrower_profile,0,150).'...';
            } else {
                $borrowers[$i]['borrower_profile'] = $borrower_profile;
            }
        }
        $this -> assign('borrowers', $borrowers);
    }

    // 登出系统
    public function logout(){
        session(null);  //清除全部session信息
        $this -> redirect('Index/index');
    }

    // 注册界面跳转
    public function register(){
        $this -> redirect('User/register');
    }
}