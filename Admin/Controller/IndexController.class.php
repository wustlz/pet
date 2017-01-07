<?php
namespace Admin\Controller;
use Think\Controller;

class IndexController extends Controller {
    public function index(){
        if(empty(session('admin'))){
            $this->display('sign-in');
        } else {
            // 加载管理员信息
            $this->loadAdmin();
            $this->assign('curMethod','Administrators');
            $this->display('index');
        }
    }

    public function loginAdmin(){
        if(!empty($_POST)){
            // 实例化UserLoginModel
            $adminModel = new \Model\AdminModel();
            $admin = $adminModel -> checkNamePwd($_POST['admin_name'],$_POST['admin_password']);
            if($admin === false){
                $this->assign('error_msg','fail');
                $this->display('sign-in');
            } else {
                // 获取当前时间并修改login_time
                $admin['admin_time'] = date('y-m-d h:i:s',time());
                $adminModel->save($admin);
                session(null);
                session('admin',$admin);
                // 设置cookie
                if($_POST['remember_admin']==1){
                    cookie($admin['admin_name'],array($admin['admin_name'],$admin['admin_password']),3600);
                }
                // 跳转到后台首页
                $this->index();
            }
        } else {
            $this->display('sign-in');
        }
    }

    public function logout(){
        session(null);
        $this->display('sign-in');
    }

    public function resetpwd(){
        if(empty(session('admin'))){
            $this->display('sign-in');
        } else if(!empty($_POST)){
            if(empty($_POST['password'])){
                $this->assign('error_msg','Please input old password');
            } else if(empty($_POST['password1'])){
                $this->assign('error_msg','Please input new password');
            } else if($_POST['password1']!=$_POST['password2']){
                $this->assign('error_msg','Two password entries are inconsistent');
            } else {
                $adminModel = new \Model\AdminModel();
                $admin = $adminModel -> checkNamePwd(session('admin')['admin_name'],$_POST['password']);
                if($admin===false){
                    $this->assign('error_msg','Please input the correct old password');   //密码错误
                } else {
                    $admin['admin_password'] = md5($_POST['password1']);
                    $rst = $adminModel->where('admin_id='.$admin['admin_id'])->save($admin);
                    if($rst){
                        $this->index();
                        return;
                    } else {
                        $this->assign('error_msg','Please refresh and try it again');
                    }
                }
            }
        }
        $this->display();
    }

    public function loadAdmin(){
        if(empty(session('admin'))){
            $this->display('sign-in');
            return;
        } else {
            $adminModel = M('Admin');
            $admins = $adminModel->where('admin_name!="admin" and admin_name!="'.session('admin')['admin_name'].'"')->order('admin_time desc')->select();
            $this->assign('adminlist',$admins);
        }
    }

    public function delAdmin(){
        if(empty(session('admin'))){
            $this->display('sign-in');
            return;
        } else if(!empty($_GET['id'])){
            if($_GET['id']==session('admin')['admin_id']){
                $this->assign('backMsg',"You can't delete your account!");
            } else {
                $adminModel = M('Admin');
                $rst = $adminModel->where('admin_id='.$_GET['id'])->delete();
                if($rst){
                    $this->assign('backMsg',"Successfully deleted!");
                } else {
                    $this->assign('backMsg',"Failed to delete!");
                }
            }
            // 加载管理员信息
            $this->index();
        }
    }

    public function addAdmin(){
        if(empty(session('admin'))){
            $this->display('sign-in');
            return;
        } else if(!empty($_POST)){
            if(empty($_POST['admin_name'])){
                $this->assign('backMsg','Please input Username');
            } else if(empty($_POST['admin_email'])){
                $this->assign('backMsg','Please input E-mail');
            } else if(empty($_POST['admin_phone'])){
                $this->assign('backMsg','Please input phone number');
            } else {
                // show_bug($_POST);
                $adminModel = M('Admin');
                // 判断用户名是否重复
                $admin = $adminModel->where('admin_name="'.$_POST['admin_name'].'"')->find();
                if($admin){
                    $this->assign('backMsg',"The Username is already in use!");
                } else {
                    $admin['admin_name'] = $_POST['admin_name'];
                    $admin['admin_email'] = $_POST['admin_email'];
                    $admin['admin_phone'] = $_POST['admin_phone'];
                    $admin['admin_password'] = md5('admin');
                    $admin['admin_time'] = date('y-m-d h:i:s',time());
                    // show_bug($admin);
                    $rst = $adminModel->add($admin);
                    if($rst){
                        $this->assign('backMsg',"Successfully Add!");
                        $this->index();
                        return;
                    } else {
                        $this->assign('backMsg',"Failed to add!");
                    }
                }
            }
        }
        $this->assign('curMethod','Administrators');
        $this->display();
    }

    // 前台首页信息管理
    public function frontHome(){
        if(empty(session('admin'))){
            $this->display('sign-in');
            return;
        } else if(!empty($_POST)){
            // show_bug($_POST);
            $indexModel = new \Model\IndexModel();
            if(!$indexModel -> create()){
                $backInfo = $indexModel->getError();
            } else {
                $indexModel->where('index_id='.$_POST['index_id'])->save();
                $backInfo = array('Update Home Page Info Successfully!');
            }
            $this->assign('backInfo',$backInfo);
        }
        $indexModel = M("Index");
        $index = $indexModel->find();
        $this->assign('index',$index);
        $this->assign('curMethod','Home');
        $this->display('home');
    }
}