<?php
namespace Admin\Controller;
use Think\Controller;

class UserController extends Controller {
    
    public function owner(){
        if(empty(session('admin'))){
            $this->display('/Index/sign-in');
        } else {
        	$userLoginModel = M('User_login');
        	// 获取当前记录总条数
	        $total = $userLoginModel -> where('user_type=1') -> count();
            if($total>0){
    	        $per = 10;   // 每页显示数
    	        // 实例化分页类对象
    	        $page = new \Component\Page($total, $per);
    	        // 拼装sql语句获得每页信息
    	        $sql = "select * from pet_user_login where user_type=1 order by login_time desc ".$page->limit;
    	        // 获取user_login表记录
    	        $userLogins = $userLoginModel -> query($sql);
    	        
    	        $userModel = M('User');
    	        $begModel = M('Beg');

            	$size = count($userLogins);

            	for($i=0; $i<$size; $i++){
            		// 获取user表信息
            		$user = $userModel->where('user_id='.$userLogins[$i]['user_id'])->find();
            		// 获取beg信息条数
            		$beg_count = $begModel->where('user_id='.$userLogins[$i]['user_id'])->count();

            		// 将相关信息封装到数组
            		$owners[$i]['userLogin'] = $userLogins[$i];
            		$owners[$i]['user'] = $user;
            		$owners[$i]['beg_count'] = $beg_count;
            	}
            	$this -> assign("ownerlist", $owners);

            	// 获得页码页表
            	$pagelist = $page -> fpage();
            	$this -> assign("pagelist", $pagelist);
            }

            $this->assign('curMethod','Owners');
            $this->display('owner');
        }
    }

    public function delOwner(){
    	if(empty(session('admin'))){
            $this->display('/Index/sign-in');
        } else if(!empty($_GET['id'])){
        	$userLoginModel = M('User_login');
        	$userLoginModel->where('user_id='.$_GET['id'])->delete();
        	$userModel = M('User');
        	$userModel->where('user_id='.$_GET['id'])->delete();
        	//还有一些连带删除表，beg order beg_pet
        	// 读取beg记录
        	$begModel = M('Beg');
	        $begpetModel = M('Beg_pet');
	        $orderModel = M('Order');

        	$beg = $begModel->where('user_id='.$_GET['id'])->select();
        	if(!empty($beg)){
	        	$size=count($beg);
	        	for($i=0; $i<$size; $i++) {
		        	$begpet = $begpetModel -> where('beg_pet_id='.$beg[$i]['beg_pet_id']) -> find();
		        	unlink('./public/upload/'.$begpet['beg_pet_small_photo']);
		        	unlink('./public/upload/'.$begpet['beg_pet_big_photo']);
		        	$begpetModel -> where('beg_pet_id='.$beg[$i]['beg_pet_id']) -> delete();
		        	$orderModel -> where('beg_id='.$beg[$i]['beg_id']) -> delete();
	        	}
        	}
        	$begModel->where('user_id='.$_GET['id'])->delete();

        	$this->owner();
        	return;
        }
    }

    public function borrower(){
		if(empty(session('admin'))){
            $this->display('/Index/sign-in');
        } else {
        	$userLoginModel = M('User_login');
        	// 获取当前记录总条数
	        $total = $userLoginModel -> where('user_type=2') -> count();
            if($total>0){
    	        $per = 2;   // 每页显示数
    	        // 实例化分页类对象
    	        $page = new \Component\Page($total, $per);
    	        // 拼装sql语句获得每页信息
    	        $sql = "select * from pet_user_login where user_type=2 order by login_time desc ".$page->limit;
    	        // 获取user_login表记录
    	        $userLogins = $userLoginModel -> query($sql);
    	        
    	        $userModel = M('User');
    	        $borrowerModel = M('Borrower');

            	$size = count($userLogins);
            	for($i=0; $i<$size; $i++){
            		// 获取user表信息
            		$user = $userModel->where('user_id='.$userLogins[$i]['user_id'])->find();
            		// 获取borrower表信息
            		$borrower = $borrowerModel->where('user_id='.$userLogins[$i]['user_id'])->find();
            		// 将相关信息封装到数组
            		$borrowers[$i]['userLogin'] = $userLogins[$i];
            		$borrowers[$i]['user'] = $user;
            		$borrowers[$i]['borrower'] = $borrower;
            	}
            	$this -> assign("borrowerlist", $borrowers);

            	// 获得页码页表
            	$pagelist = $page -> fpage();
            	$this -> assign("pagelist", $pagelist);
            }

            $this->assign('curMethod','Borrowers');
            $this->display('borrower');
        }
    }

    public function delBorrower(){
    	if(empty(session('admin'))){
            $this->display('/Index/sign-in');
        } else if(!empty($_GET['id'])){
        	$userLoginModel = M('User_login');
        	$userLoginModel->where('user_id='.$_GET['id'])->delete();
        	$userModel = M('User');
        	$userModel->where('user_id='.$_GET['id'])->delete();

        	$borrowerModel = M('Borrower');
        	$borrower = $borrowerModel->where('user_id='.$_GET['id'])->find();
        	if(!empty($borrower)){
				unlink('./public/upload/'.$borrower['borrower_photo']);

	        	//order 表删除
		        $orderModel = M('Order');
		        $orderModel->where('borrower_id='.$borrower['borrower_id'])->delete();

				$borrowerModel->where('user_id='.$_GET['id'])->delete();
			}

        	$this->borrower();
        	return;
        }
    }
}