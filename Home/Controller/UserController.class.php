<?php
namespace Home\Controller;
use Think\Controller;

class UserController extends Controller {

    // 登录
    public function login(){
        $data['status'] = 0;
        if(!empty($_POST)){
        	// 实例化UserLoginModel
            $userLogin = new \Model\UserLoginModel();
            $userLoginInfo = $userLogin -> checkEmailPwd($_POST['user_email'],$_POST['user_password']);
            if($userLoginInfo === false){
                $data['error'] = "The mailbox or password is incorrect";
            } else {
                // 获取当前时间并修改login_time
                $userLoginInfo['login_time'] = date('y-m-d h:i:s',time());
                $userLogin->save($userLoginInfo);
                // 根据登录关联的user_id 查询对应的用户记录
                $userModel = M("User");
                $user = $userModel -> where('user_id='.$userLoginInfo['user_id']) -> find();
                session(null);
                session('user_firstname',$user['user_firstname']);
                session('user_type',$userLoginInfo['user_type']);
                session('user',$userLoginInfo['user_id']);
                // 设置cookie
                if($_POST['remember_user']==1){
                    cookie($user['user_firstname'],array($user['user_firstname'].' '.$user['user_lastname'],$userLoginInfo['user_password']),3600);
                }
                // $data['user_firstName'] = $user['user_firstname'];
                $data['status'] = 1;
            }
        }
        // 将对应信息通过json格式返回
        $this -> ajaxReturn($data,'JSON');
    }

    // 用户注册
    public function register(){

        // 获取公共信息
        R('PublicInfo/headerFooter');

        if(!empty($_POST)){
            $registerOk = false;
            // 实例化UserModel
            $userLogin = new \Model\UserLoginModel();
            // 验证用户注册信息是否符合规范!$user -> create() 
            if(!$userLogin -> create()){
                $registerInfo = $userLogin->getError();
            } else {
                //将用户信息添加到user表，返回user_id
                $user = M('User');
                $user->create();
                $user_id = $user->add();
                if($user_id){
                    //用户信息添加成功，则将该用户添加到user_login 表
                    $userLogin->user_id = $user_id;
                    $userLogin->user_password = md5($_POST['user_password']);
                    $userLogin->login_time = date('y-m-d h:i:s',time());
                    $rst = $userLogin->add();
                    if($rst){
                        $registerOk = true;
                        session(null);
                        session('user',$user_id);
                        session('user_firstname',$_POST['user_firstname']);
                        session('user_type',$_POST['user_type']);
                    } else {
                        //删除已添加到user表的记录
                        $user->where('user_id='.$user_id)->delete();
                    }
                }
            }
            if($registerOk){
                // 跳转到个人中心
                $this -> redirect('User/userCenter');
            } else {
                $this -> assign('registerInfo',$registerInfo);
                $this -> display();
            }
        } else {
            $this -> display();
        }
    }

    // 用户中心
    public function userCenter(){
        if(session('user')==null || session('user')==''){
            $this -> redirect('Index/index');
        } else {
            // 获取公共信息
            R('PublicInfo/headerFooter');
            // 1、获取用户个人信息
            // 根据session中的user_id 查找对应的用户信息
            $userModel = M("User");
            $user = $userModel -> where('user_id='.session('user')) -> find();
            // 查找登录信息
            $UserLoginModel = M('UserLogin');
            $userLogin = $UserLoginModel -> where('user_id='.session('user')) -> find();
            // 输出到模板
            $this -> assign('user', $user);
            $this -> assign('userLogin', $userLogin);

            // 2、若是borrower，则加载相应的borrower信息
            //    若是owner，则加载相应的beg发布宠物信息
            if($userLogin['user_type']==1){ //owner
                R('Pet/listBegByOwner');
                R('Beg/loadBeg');
            } else if($userLogin['user_type']==2){  //borrwer
                R('Borrower/loadBorrower');
            }
            $this -> display();
        }
    }

    // 修改用户个人信息
    public function upt_personInfo(){
        if(session('user')==null || session('user')==''){
            $this -> redirect('Index/index');
        } else {
            $data = 0;
            // 获取当前User
            $userModel = M("User");
            $user = $userModel -> where('user_id='.session('user')) -> find();
            if($user['user_phone'] == $_POST['user_phone'] && $user['user_location'] = $_POST['user_location']){
                $data = 1;
            } else {
                $user['user_phone'] = $_POST['user_phone'];
                $user['user_location'] = $_POST['user_location'];
                $rst = $userModel->save($user);
                if($rst){
                    $data = 1;
                }
            }
            $this->ajaxReturn($data);
        }
    }
}