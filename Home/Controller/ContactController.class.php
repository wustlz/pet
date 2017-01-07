<?php
namespace Home\Controller;
use Think\Controller;
class ContactController extends Controller {
    
    // 获取head上的联系方式及foot
    public function contact(){
        R('PublicInfo/headerFooter');
        if(!empty($_POST)){
            $contactModel = new \Model\ContactModel();
            $_POST['contact_time'] = date('Y-m-d H:i:s');
            if(!$contactModel->create()){
                $this->assign('back_info',$contactModel->getError());
                // show_bug($contactModel->getError());
            } else {
                $rst = $contactModel->add();
                // show_bug($rst);
                if($rst){
                    $back_info[0] = 'We will contact you as soon as possible!';
                } else {
                    $back_info[0] = 'Network error, please refresh and try again!';
                }
                $this->assign('back_info',$back_info);
            }
        }
        $this -> display();
    }

}