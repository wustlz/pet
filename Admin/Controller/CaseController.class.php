<?php
namespace Admin\Controller;
use Think\Controller;

class CaseController extends Controller {

    public function caselist(){
        if(empty(session('admin'))){
            $this->display('/Index/sign-in');
        } else {
            $this->loadCase();
            $this->assign('curMethod','case');
            $this->display();
        }
    }

    public function addCase(){
        if(empty(session('admin'))){
            $this->display('/Index/sign-in');
            return;
        } else if(!empty($_POST)){
            if(empty($_POST['case_title'])){
                $this->assign('error_msg','Please input title');
            } else if(empty($_POST['case_content'])){
                $this->assign('error_msg','Please input content');
            } else if(empty($_FILES)){
                $this->assign('error_msg','Please upload photo');
            } else {
                // 上传图片
                $config = array(
                   'rootPath'  =>  './public/upload/', //根目录
                   'savePath'  =>  'case/', //
                );
                $upload = new \Think\Upload($config);
                $z = $upload -> uploadOne($_FILES['case_photo']);
                if(!$z){
                    $this->assign('error_msg','Please refresh and upload photo again');
                } else {
                    // 拼装图片路径
                    $bigimg = $z['savepath'].$z['savename'];
                    $_POST['case_big_photo'] = $bigimg;
                    //制作缩略图Image.class.php
                    $image = new \Think\Image();
                    $srcimg = $upload->rootPath.$bigimg;
                    $image -> open($srcimg);
                    $image -> thumb(150,150);   //自适应按比例缩放
                    $smallimg = $z['savepath']."small_".$z['savename'];
                    $image -> save($upload->rootPath.$smallimg);
                    $_POST['case_small_photo'] = $smallimg;

                    // 添加时间戳
                    $_POST['case_time'] = date('Y-m-d H:i:s');
                    $caseModel = M('Case');
                    $caseModel->create();
                    $rst = $caseModel->add();
                    if($rst){
                        $this->assign('error_msg','Successfully add case!');
                    } else {
                        $this->assign('error_msg','Network error, Please refresh and try again!');
                    }
                }
            }
        }
        $this->assign('curMethod','case');
        $this->display('caseadd');
    }

    public function uptCase(){
        if(empty(session('admin'))){
            $this->display('/Index/sign-in');
            return;
        } elseif (!empty($_GET['id'])) {
            // 获取当前case信息
            $caseModel = M('Case');
            $case = $caseModel->where('case_id='.$_GET['id'])->find();
        } else if(!empty($_POST)){
            // 获取当前case信息
            $caseModel = M('Case');
            $case = $caseModel->where('case_id='.$_POST['case_id'])->find();
            if(empty($case)){
                $this->loadCase();
                $this->display('caselist');
                return;
            } else if(empty($_POST['case_title'])){
                $this->assign('error_msg','Please input title');
            } else if(empty($_POST['case_content'])){
                $this->assign('error_msg','Please input content');
            } else {
                if(!empty($_FILES)){
                    // 上传图片
                    $config = array(
                       'rootPath'  =>  './public/upload/', //根目录
                       'savePath'  =>  'case/', //
                    );
                    $upload = new \Think\Upload($config);
                    $z = $upload -> uploadOne($_FILES['case_photo']);
                    if(!$z){
                        $this->assign('error_msg','Please refresh and upload photo again');
                    } else {
                        // 删除当前图片
                        unlink('./public/upload/'.$case['case_big_photo']);
                        unlink('./public/upload/'.$case['case_small_photo']);
                        // 拼装图片路径
                        $bigimg = $z['savepath'].$z['savename'];
                        $case['case_big_photo'] = $bigimg;
                        //制作缩略图Image.class.php
                        $image = new \Think\Image();
                        $srcimg = $upload->rootPath.$bigimg;
                        $image -> open($srcimg);
                        $image -> thumb(150,150);   //自适应按比例缩放
                        $smallimg = $z['savepath']."small_".$z['savename'];
                        $image -> save($upload->rootPath.$smallimg);
                        $case['case_small_photo'] = $smallimg;
                    }
                }
                // 添加时间戳
                $case['case_time'] = date('Y-m-d H:i:s');
                
                $case['case_title'] = $_POST['case_title'];
                $case['case_content'] = $_POST['case_content'];

                $rst = $caseModel->save($case);

                if($rst){
                    $this->assign('error_msg','Successfully update case!');
                } else {
                    $this->assign('error_msg','Network error, Please refresh and try again!');
                }
            }
        }
        $this->assign('case',$case);
        $this->assign('curMethod','case');
        $this->display('caseupt');
    }

    public function delCase(){
        if(empty(session('admin'))){
            $this->display('/Index/sign-in');
        } else if(!empty($_GET['id'])){
            $caseModel = M('Case');
            $case = $caseModel->where('case_id='.$_GET['id'])->find();
            // 删除图片
            unlink('./public/upload/'.$case['case_small_photo']);
            unlink('./public/upload/'.$case['case_big_photo']);
            // 删除对应记录
            $caseModel->where('case_id='.$_GET['id'])->delete();
            
            $this->loadCase();
            $this->assign('curMethod','case');
            $this->display('caselist');
        }
    }

    function loadCase(){
        $caseModel = M("Case");
        // 获取当前记录总条数
        $total = $caseModel->count();
        if($total>0){
            $per = 3;   // 每页显示数
            // 实例化分页类对象
            $page = new \Component\Page($total, $per);
            // 拼装sql语句获得每页信息
            $sql = "select * from pet_case order by case_time desc ".$page->limit;
            $caselist = $caseModel -> query($sql);
            $this->assign('caselist', $caselist);
            // 获得页码页表
            $pagelist = $page -> fpage();
            $this -> assign("pagelist", $pagelist);
        }
    }
}