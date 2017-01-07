<?php
namespace Home\Controller;
use Think\Controller;
class BorrowerController extends Controller {

    // borrower信息列表
    public function listBorrower(){
        $borrowerModel = M("Borrower");
        // 获取当前记录总条数
        $total = $borrowerModel->where('borrower_photo !="" and borrower_profile !="" ') -> count();
        if($total>0){
            $per = 9;   // 每页显示数
            // 实例化分页类对象
            $page = new \Component\Page($total, $per);
            // 拼装sql语句获得每页信息
            $sql = "select * from pet_borrower where borrower_photo !='' and borrower_profile !='' order by borrower_count desc ".$page->limit;
            $info = $borrowerModel -> query($sql);
            // 获取beg_pet及order信息
            $this->queryUserFromBorrower($info);
            // 获得页码页表
            $pagelist = $page -> fpage();
            $this -> assign('pagelist', $pagelist);
        }
        // 获取公共信息
        R('PublicInfo/headerFooter');
        $this->display();
    }

    function queryUserFromBorrower($borrowers){
        $size = count($borrowers);
        $userModel = M("User");
        for($i=0; $i<$size; $i++){
            $borrower = $borrowers[$i];
            $borrower_profile = preg_replace('/[　|\s|\n|\r|\t]+/', ' ', $borrower['borrower_profile'].trim());
            if(strlen($borrower_profile)>140){
                $borrower['borrower_profile'] = substr($borrower_profile,0,140).'...';
            } else {
                $borrower['borrower_profile'] = $borrower_profile;
            }
            $borrowerList[$i]['borrower'] = $borrower;
            $user = $userModel->where('user_id='.$borrowers[$i]['user_id'])->find();
            $borrowerList[$i]['user'] = $user;
        }
        $this -> assign("borrowerList", $borrowerList);
    }

    public function detailBorrower(){
        R('PublicInfo/headerFooter');
        if(!empty($_GET['id'])){
            $borrowerModel = M('Borrower');
            $sql = "SELECT b.*, u.*, l.user_email FROM pet_borrower AS b, pet_user AS u, pet_user_login AS l WHERE b.user_id = u.user_id AND b.user_id = l.user_id AND b.borrower_id = ".$_GET['id'];
            $borrowers = $borrowerModel->query($sql);
            if(!empty($borrowers)){
                $this->assign('borrower', $borrowers[0]);
                $this->display('detailBorrower');
                return;
            }
        }
        $this->redirect('Borrower/listBorrower');
    }
    
    // 根据当前登录用户获取borrower信息
    public function loadBorrower(){
        if(session('user')==null || session('user')==''){
            $this -> redirect('Index/index');
        } else {
            $borrowerModel = M('Borrower');
            $borrower = $borrowerModel->where('user_id='.session('user'))->find();
            if(!empty($borrower)){
                $this -> assign('borrower', $borrower);
                // 根据borrower查询order及相关信息
                $orderModel = M("Order");
                $total = $orderModel->where('borrower_id='.$borrower['borrower_id'])->count();
                if($total>0){
                    $per = 9;   // 每页显示数
                    // 实例化分页类对象
                    $page = new \Component\Page($total, $per);
                    // 拼装sql语句获得每页信息
                    $sql = 'SELECT * FROM pet_order WHERE borrower_id='.$borrower['borrower_id'].' ORDER BY order_create desc';
                    $orders = $orderModel->query($sql);
                    if(!empty($orders)){
                        $size = count($orders);
                        $begModel = M("Beg");
                        $petModel = M("Pet");
                        for($i=0; $i<$size; $i++){
                            // 获取beg, owner信息
                            $sql = 'SELECT b.*, u.user_firstname, u.user_lastname FROM pet_beg AS b, pet_user AS u WHERE b.user_id = u.user_id AND b.beg_id = '.$orders[$i]['beg_id'];
                            $begs = $begModel->query($sql);
                            if(!empty($begs)){
                                $beg = $begs[0];
                                // 获取对应的宠物信息
                                $listPet_id = explode(',',$beg['pet_ids']);
                                foreach ($listPet_id as $key => $pet_id) {
                                    $listPet[$key] = $petModel->where('pet_id='.$pet_id)->find();
                                }
                                $beg['listPet'] = $listPet;
                                $orderlist[$i]['order']=$orders[$i];
                                $orderlist[$i]['beg']=$beg;
                            }
                        }
                        $this -> assign('orderlist', $orderlist);
                        // 获得页码页表
                        $pagelist = $page -> fpage();
                        $this -> assign('pagelist', $pagelist);
                    }
                }
            }
        }
    }
    // 修改用户信息
    public function uptBorrowerProfile(){
        // 首先获取当前的borrwer信息
        $borrowerModel = M('Borrower');
        $borrower = $borrowerModel->where('user_id='.session('user'))->find();
        // show_bug($borrower);
        $rst = 0;
        if(empty($borrower)){   //为空则添加信息
            $borrower['user_id'] = session('user');
            $borrower['borrower_photo'] = '';
            $borrower['borrower_profile'] = $_POST['borrower_profile'];
            $borrower['borrower_count'] = 0;
            $rst = $borrowerModel->add($borrower);
        } else {
            $borrower['borrower_profile'] = $_POST['borrower_profile'];
            $rst = $borrowerModel->where('user_id='.session('user'))->save($borrower);
        }
        if($rst){
            $data = 1;
        } else {
            $data = 0;
        }
        $this -> ajaxReturn($data);
    }

    // 上传头像
    public function uploadPhoto(){
        R('PublicInfo/headerFooter');
        if(session('user')==null || session('user')==''){
            $this -> redirect('Index/index');
        } else if(empty($_FILES)){
            $this -> redirect('User/userCenter');
        } else {
            $config = array(
               'rootPath'  =>  './public/upload/', //根目录
               'savePath'  =>  'photo/', //
            );
            $upload = new \Think\Upload($config);
            $z = $upload -> uploadOne($_FILES['borrower_photo']);
            if(!$z){
                // $this->assign('back_info',$upload->getError());
            } else {
               // 拼装图片路径
               $bigimg = $z['savepath'].$z['savename'];
                // 获取borrower信息
                $borrowerModel = M("Borrower");
                $borrower = $borrowerModel->where("user_id=".session('user'))->find();
                if(empty($borrower)){
                    $borrower['user_id'] = session('user');
                    $borrower['borrower_photo'] = $bigimg;
                    $borrower['borrower_count'] = 0;
                    $borrower['borrower_profile'] = '';
                    $borrowerModel->add($borrower);
                } else {
                    // 首先删除已有头像
                    unlink('./public/upload/'.$borrower['borrower_photo']);
                    $borrower['borrower_photo'] = $bigimg;
                    $borrowerModel->where('borrower_id='.$borrower['borrower_id'])->save($borrower);
                }
                $this -> redirect('User/userCenter');
           }
        }
    }

}