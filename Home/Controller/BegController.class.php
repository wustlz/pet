<?php
namespace Home\Controller;
use Think\Controller;
class BegController extends Controller {

    // 列出宠物发布信息
    public function listBeg(){
        $begModel = M("Beg");
        // 获取当前记录总条数
        $total = $begModel -> count();
        if($total>0){
            $per = 3;   // 每页显示数
            // 实例化分页类对象
            $page = new \Component\Page($total, $per);
            // 拼装sql语句获得每页信息,左连接查询，将未被接受的beg优先显示
            $sql = "select pet_beg.* from pet_beg LEFT JOIN pet_order ON pet_order.beg_id = pet_beg.beg_id order by pet_order.order_work ASC, pet_beg.beg_time desc ".$page->limit;
            $info = $begModel -> query($sql);
            // 获取beg_pet及order信息
            $this->queryBegPet($info);
            // 获得页码页表
            $pagelist = $page -> fpage();
            $this -> assign('pagelist', $pagelist);
        }
        // 获取公共信息
        R('PublicInfo/headerFooter');
        R('Case/getCases');
        $this->display();
    }

    public function search(){
        $begModel = M("Beg");
        if(!empty($_POST)){
            session('search_beg_key', $_POST['search_key']);
        }
        // 获取当前记录总条数
        $total = $begModel->where("beg_title like '%".session('search_beg_key')."%'") -> count();
        $per = 3;   // 每页显示数
        // 实例化分页类对象
        $page = new \Component\Page($total, $per);
        // 拼装sql语句获得每页信息
        $sql = "select * from pet_beg where beg_title like '%".session('search_beg_key')."%' order by beg_time desc ".$page->limit;
        $info = $begModel -> query($sql);
        if(!empty($info)){
            // 获取beg_pet及order信息
            $this->queryBegPet($info);
            // 获得页码页表
            $pagelist = $page -> fpage();
            $this -> assign('pagelist', $pagelist);
        }
        // 获取公共信息
        R('PublicInfo/headerFooter');
        R('Case/getCases');
        $this->display('listBeg');
    }

    // 显示详细的beg有关信息
    public function detailBeg(){
        if(empty($_GET['id'])){
            $this->redirect('Beg/listBeg');
        } else {
            // 根据传递过来的id查询相关信息
            $begModel = M("Beg");
            $beg = $begModel -> where('beg_id='.$_GET['id']) -> find();
            // 封装beg信息
            $begInfo['beg'] = $beg;

            // 获取当前的beg对应的宠物id
            $pet_ids = explode(',',$beg['pet_ids']);
            // 查询相应的pet集合
            $petModel = M('Pet');
            // 拼接sql语句
            foreach ($pet_ids as $key => $pet_id) {
                if($key==0){
                    $condition = 'pet_id='.$pet_id.' ';
                } else {
                    $condition = $condition.'or pet_id='.$pet_id.' ';
                }
                // $listPet[$key] = $petModel->where('pet_id='.$pet_id)->find();
            }
            $sql = 'select pet_pet.*, breed_name from pet_pet, pet_breed where pet_pet.breed_id = pet_breed.breed_id and ('.$condition.')';
            $begInfo['listPet'] = $petModel->query($sql);

            // 查询成交信息
            $orderModel = M("Order");       //成交信息
            $order = $orderModel->where('beg_id='.$beg['beg_id'])->find();
            if(empty($order)){
                $begInfo['beg_work'] = 2;
            } else {
                $begInfo['beg_work'] = $order['order_work'];
            }
            // 封装order信息
            $begInfo['order'] = $order;
            $this -> assign('begInfo', $begInfo);

            // 分割时间
            $this -> assign('year', date('Y', strtotime($beg['beg_time'])));
            $this -> assign('month', date('M', strtotime($beg['beg_time'])));
            $this -> assign('day', date('d', strtotime($beg['beg_time'])));

            // 获取最近的6条beg信息
            $this->getRecentBegs(6);

            // 获取公共信息
            R('PublicInfo/headerFooter');
            $this->display();
        }
    }

    // 查询最近的6条信息
    public function getRecentBegs($pageSize){
        $begModel = M('Beg');
        $info = $begModel->order('beg_time desc')->limit(0,6)->select();
        if(!empty($info)){
            $this->queryBegPet($info);
        }
    }
    
    // 根据当前登录用户获取beg信息
    public function loadBeg(){
        if(session('user')==null || session('user')==''){
            $this -> redirect('Index/index');
        } else {
            $begModel = M('Beg');
            // 获取当前记录总条数
            $total = $begModel->where('user_id='.session('user')) -> count();
            if($total>0){
                $per = 2;   // 每页显示数
                // 实例化分页类对象
                $page = new \Component\Page($total, $per);
                // 拼装sql语句获得每页信息
                $sql = "select * from pet_beg where user_id=".session('user')." order by beg_time desc ".$page->limit;
                $listBeg = $begModel -> query($sql);
                // show_bug($listBeg);
                if(!empty($listBeg)){
                    $this->queryBegPet($listBeg);
                    // 获得页码页表
                    $pagelist = $page -> fpage();
                    $this -> assign('pagelist', $pagelist);
                }
            }
        }
    }

    // 根据已查询的beg信息查询其对应的宠物、成交信息，并将查询结果封装到array中
    private function queryBegPet($listBeg){
        $size = count($listBeg);
        $beglist = array();
        $petModel = M("Pet");    //宠物信息
        $orderModel = M("Order");       //成交信息
        for($i=0; $i<$size; $i++){
            // 封装beg信息
            $beglist[$i]['beg'] = $listBeg[$i];
            // 获取当前的beg中的宠物id
            $pet_ids = explode(',',$listBeg[$i]['pet_ids']);
            // 查询相应的pet集合
            foreach ($pet_ids as $key => $pet_id) {
                $tempPet = $petModel->where('pet_id='.$pet_id)->find();
                $listPet[$key] = $tempPet;
                if($key==0){
                    $beglist[$i]['beg']['beg_photo'] = $tempPet['pet_big_photo'];
                }
            }
            // 封装beg_pet信息
            $beglist[$i]['listPet'] = $listPet;
            // 查询相应的order，确认是否完成
            $order = $orderModel->where('beg_id='.$listBeg[$i]['beg_id'])->find();
            if(empty($order)){
                $beglist[$i]['beg_work'] = -1;  //waiting
            } else {
                $beglist[$i]['beg_work'] = $order['order_work'];
                $beglist[$i]['order_id'] = $order['order_id'];
            }
            // 封装order信息
            $beglist[$i]['order'] = $order;
            // 封装分隔开的时间信息
            $beg_ymd['year'] = date('Y', strtotime($listBeg[$i]['beg_time']));
            $beg_ymd['month'] = date('M', strtotime($listBeg[$i]['beg_time']));
            $beg_ymd['day'] = date('d', strtotime($listBeg[$i]['beg_time']));
            $beglist[$i]['beg_ymd'] = $beg_ymd;
        }
        $this -> assign('beglist', $beglist);
        return $beglist;
    }

    // 添加
    public function add(){
        R('PublicInfo/headerFooter');
        if(session('user')==null || session('user')==''){
            $this -> redirect('Index/index');
            return;
        } else if(!empty($_POST)){
            // show_bug($_POST);
            // 验证
            $begModel = new \Model\BegModel();
            if(!$begModel->create()){
                $this->assign('back_info',$begModel->getError());
            } else {
                $_POST['pet_ids'] = join(',',$_POST['pet_ids']);
                $_POST['user_id'] = session('user');
                $_POST['beg_time'] = date('Y-m-d H:i:s');
                $begModel->create();
                $rst = $begModel->add();
                if($rst){
                    $this->assign('back_info','success');
                } else {
                    $this->assign('back_info',array('Network error, please refresh and try id again!'));
                }
            }
        }
        // 1、获取用户个人信息
        // 根据session中的user_id 查找对应的用户信息
        $userModel = M("User");
        $user = $userModel -> where('user_id='.session('user')) -> find();
        // 查找登录信息
        $UserLoginModel = M('UserLogin');
        $userLogin = $UserLoginModel -> where('user_id='.session('user')) -> find();
        // 获取当前用户对应的pet
        if(!empty($user['pet_ids'])){
            $listPet_id = explode(',',$user['pet_ids']);
            $petModel = M("Pet");
            foreach ($listPet_id as $key => $pet_id) {
                $listPet[$key] = $petModel->where('pet_id='.$pet_id)->find();
            }
        }
        // show_bug($listPet);
        // 输出到模板
        $this -> assign('user', $user);
        $this -> assign('userLogin', $userLogin);
        $this -> assign('listPet', $listPet);
        $this -> display('addBeg');
    }

    public function delete(){
        R('PublicInfo/headerFooter');
        $data='error';
        if(!(session('user')==null || session('user')=='')
            && !empty($_POST['beg_id'])){

            $begModel = M('Beg');
            $beg = $begModel->where('beg_id='.$_POST['beg_id'])->find();

            if($beg['user_id']==session('user')){
                // 删除beg，由于设置了外键级联删除，不需要手动删除与之相关的order表记录
                $rst = $begModel->where('beg_id='.$_POST['beg_id'])->delete();
                if($rst){
                    $data='success';
                }
            }
        }
        $this->ajaxReturn($data);
    }

    public function detail(){
        R('PublicInfo/headerFooter');
        if(!(session('user')==null || session('user')=='')
            && !empty($_GET['id'])){
            // 获取当前beg
            $begModel = M('Beg');
            $beg = $begModel->where('beg_id='.$_GET['id'])->find();
            if($beg['user_id']==session('user')){
                // 获取当前的beg对应的宠物id
                $pet_ids = explode(',',$beg['pet_ids']);
                // 查询相应的pet集合
                $petModel = M('Pet');
                foreach ($pet_ids as $key => $pet_id) {
                    $listPet[$key] = $petModel->where('pet_id='.$pet_id)->find();
                }
                $beg['listPet'] = $listPet;
                $this->assign('beg',$beg);
                $this->display();
            } else {
                $this->redirect('User/userCenter');
            }
        } else {
            $this->redirect('Index/index');
        }
    }

    public function complexSearch(){
        if(!empty($_POST)){
            $data['status'] = 'none';
            $condition = 'select * from pet_pet where 1=1';
            if(!empty($_POST['breed_id'])){
                foreach ($_POST['breed_id'] as $key => $value) {
                    if($key==0){
                        $condition = $condition.' and (breed_id='.$value;
                    } else {
                        $condition = $condition.' or breed_id='.$value;
                    }
                }
                $condition = $condition.')';
            }
            if(!empty($_POST['pet_have'])){
                $pet_have = join(',',$_POST['pet_have']);
                $condition =  $condition.' and pet_have like "%'.$pet_have.'%"';
            }
            if(!empty($_POST['pet_provide'])){
                $pet_provide = join(',',$_POST['pet_provide']);
                $condition =  $condition.' and pet_provide like "%'.$pet_provide.'%"';
            }
            if(!empty($_POST['pet_helpTime'])){
                $pet_helpTime = join(',',$_POST['pet_helpTime']);
                $condition =  $condition.' and pet_helptime like "%'.$pet_helpTime.'%"';
            }
            $petModel = M('Pet');
            $listPet = $petModel->query($condition);
            // show_bug($listPet);
            // 根据查找出来的pet查找对应的beg记录
            if(!empty($listPet)){
                $condition = 'select * from pet_beg where 1=1';
                // 先按like语句查找初步符合记录
                foreach ($listPet as $key => $pet) {
                    if($key==0){
                        $condition = $condition.' and (pet_ids like "%'.$pet['pet_id'].'%"';
                    } else {
                        $condition = $condition.' or pet_ids like "%'.$pet['pet_id'].'%"';
                    }
                }
                $condition = $condition.')';
                $begModel = M('Beg');
                $listBeg_1 = $begModel->query($condition);
                // 筛选不符合记录的
                $beg_count = 0;
                foreach ($listBeg_1 as $key => $beg) {
                    $pet_ids = explode(',', $beg['pet_ids']);
                    foreach ($listPet as $key => $pet) {
                        if(in_array($pet['pet_id'], $pet_ids)){
                            $listBeg[$beg_count++] = $beg;
                            break;
                        }
                    }
                }
                if(!empty($listBeg)){
                    // show_bug($listBeg);
                    // 获取beg_pet及order信息
                    $data = $this->queryBegPet($listBeg);
                    // show_bug($data);
                    $data['status'] = 'ok';
                }
            }
            $this->ajaxReturn($data);
        } else {
            R('PublicInfo/headerFooter');
            // 加载breed
            $breedModel = M('Breed');
            $breed = $breedModel->select();
            $this->assign('listBreed',$breed);
            $this->display('search');
        }
    }

}