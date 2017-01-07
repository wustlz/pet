<?php
namespace Admin\Controller;
use Think\Controller;

class ContactController extends Controller {

    public function contact(){
        if(empty(session('admin'))){
            $this->display('/Index/sign-in');
        } else {
            $this->loadContact();
            $this->assign('curMethod','Contact');
            $this->display('Index/contact');
        }
    }

    public function delContact(){
        if(empty(session('admin'))){
            $this->display('/Index/sign-in');
        } else if(!empty($_GET['id'])){
            $contactModel = M('Contact');
            $contactModel->where('contact_id='.$_GET['id'])->delete();
            $this->loadContact();
            $this->assign('curMethod','Contact');
            $this->display('Index/contact');
        }
    }

    function loadContact(){
        $contactModel = M("Contact");
        // 获取当前记录总条数
        $total = $contactModel->count();
        if($total>0){
            $per = 3;   // 每页显示数
            // 实例化分页类对象
            $page = new \Component\Page($total, $per);
            // 拼装sql语句获得每页信息
            $sql = "select * from pet_contact order by contact_time desc ".$page->limit;
            $contactlist = $contactModel -> query($sql);
            $this->assign('contactlist', $contactlist);
            // 获得页码页表
            $pagelist = $page -> fpage();
            $this -> assign("pagelist", $pagelist);
        }
    }
}