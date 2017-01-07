<?php
namespace Home\Controller;
use Think\Controller;
class PetController extends Controller {

    // 根据Owner列出宠物信息
    public function listBegByOwner(){
        if(session('user')==null || session('user')==''){
            $this -> redirect('Index/index');
        } else {
            // 获取当前用户对应的宠物id
            $userModel = M('User');
            $user = $userModel->where('user_id='.session('user'))->find();
            if(!empty($user['pet_ids'])){
                $listPet_id = explode(',',$user['pet_ids']);
                $total = count($listPet_id);
                $petModel = M("Pet");
                foreach ($listPet_id as $key => $pet_id) {
                    $listPet[$key] = $petModel->where('pet_id='.$pet_id)->find();
                }
                // 加载对应的宠物类型
                $this->getBreed($listPet);
            }
        }
    }

    public function getBreed($listPet){
        $breedModel = M("Breed");
        foreach ($listPet as $key => $pet) {
            $info = $breedModel->where('breed_id='.$pet['breed_id'])->find();
            $listPet[$key]['breed_id'] = $info['breed_name'];
        }
        $this->assign('listPet',$listPet);
    }

    // 添加
    public function add(){
        R('PublicInfo/headerFooter');
        if(session('user')==null || session('user')==''){
            $this -> redirect('Index/index');
        } else if(!empty($_POST)){
            // 验证
            $PetModel = new \Model\PetModel();
            if(!$PetModel->create()){
                $this->assign('back_info',$PetModel->getError());
            } else if (!empty($_FILES)) {
                $config = array(
                   'rootPath'  =>  './public/upload/', //根目录
                   'savePath'  =>  'pet/', //
                );
                $upload = new \Think\Upload($config);
                $z = $upload -> uploadOne($_FILES['pet_photo']);
                if(!$z){
                   $this->assign('back_info',$upload->getError());
                } else {
                    // 拼装图片路径
                    $bigimg = $z['savepath'].$z['savename'];
                    $_POST['pet_big_photo'] = $bigimg;
                    //制作缩略图Image.class.php
                    $image = new \Think\Image();
                    $srcimg = $upload->rootPath.$bigimg;
                    $image -> open($srcimg);
                    $image -> thumb(150,100);   //自适应按比例缩放
                    $smallimg = $z['savepath']."small_".$z['savename'];
                    $image -> save($upload->rootPath.$smallimg);
                    $_POST['pet_small_photo'] = $smallimg;

                    // 拼装pet_have
                    $_POST['pet_have'] = join(",",$_POST['pet_have']);
                    // 拼装pet_provide
                    $_POST['pet_provide'] = join(",",$_POST['pet_provide']);
                    // 拼装pet_helpTime
                    $_POST['pet_helpTime'] = join(",",$_POST['pet_helpTime']);
                    $PetModel->create();
                    $petInfo = $PetModel->add();
                    if($petInfo){   //添加pet成功
                        $userModel = M("User");
                        $user = $userModel->where('user_id='.session('user'))->find();
                        if(!empty($user['pet_ids'])){
                            $user['pet_ids'] = $user['pet_ids'].','.$petInfo;
                        } else {
                            $user['pet_ids'] = $petInfo;
                        }
                        $rst = $userModel->save($user);
                        if($rst){
                           $this->assign('back_info','success');
                        } else {
                           // 回滚
                           unlink('./public/upload/'.$bigimg);
                           unlink('./public/upload/'.$smallimg);
                           $begPetModel->where('pet_id='.$petInfo)->delete();
                           $this->assign('back_info','Network error, Please refresh and try again!');
                        }
                    }
                }
            }
        }
        // 加载breed信息
        $breedModel = M("Breed");
        $listBreed = $breedModel->select();
        $this->assign('listBreed', $listBreed);
        $this->display('addPet');
    }
    // 修改
    public function update(){
        R('PublicInfo/headerFooter');
        if(session('user')==null || session('user')==''){
            $this -> redirect('Index/index');
            return;
        } else if(empty($_GET['id']) && empty($_POST)){
            $this -> redirect('User/userCenter');
            return;
        } else if(!empty($_POST)){  // 提交修改
            if($this->checkPetUser($_POST['pet_id'])==-1){
                $this -> redirect('User/userCenter');
                return;
            }
            // 验证
            $petModel = new \Model\PetModel();
            if(!$petModel->create()){
                $this->assign('back_info',$petModel->getError());
                $this->display('uptPet');
                return;
            } else {
                $uptImg = false;
                if (!empty($_FILES)) {
                    $config = array(
                       'rootPath'  =>  './public/upload/', //根目录
                       'savePath'  =>  'pet/', //
                    );
                    $upload = new \Think\Upload($config);
                    $z = $upload -> uploadOne($_FILES['pet_photo']);
                    if(!$z){
                       $this->assign('back_info',$upload->getError());
                    } else {
                        // 拼装图片路径
                        $bigimg = $z['savepath'].$z['savename'];
                        $_POST['pet_big_photo'] = $bigimg;
                        //制作缩略图Image.class.php
                        $image = new \Think\Image();
                        $srcimg = $upload->rootPath.$bigimg;
                        $image -> open($srcimg);
                        $image -> thumb(150,100);   //自适应按比例缩放
                        $smallimg = $z['savepath']."small_".$z['savename'];
                        $image -> save($upload->rootPath.$smallimg);
                        $_POST['pet_small_photo'] = $smallimg;
                        $uptImg = true;
                    }
                } 
            }            
            // 获取当前图片
            $pet = $petModel->where('pet_id='.$_POST['pet_id'])->find();
            $big_photo = $pet['pet_big_photo'];
            $small_photo = $pet['pet_small_photo'];
            // 拼装pet_have
            $_POST['pet_have'] = join(",",$_POST['pet_have']);
            // 拼装pet_provide
            $_POST['pet_provide'] = join(",",$_POST['pet_provide']);
            // 拼装pet_helpTime
            $_POST['pet_helpTime'] = join(",",$_POST['pet_helpTime']);

            // 修改
            $petModel->create();
            $petInfo = $petModel->where('pet_id='.$_POST['pet_id'])->save();

            if($petInfo){   //修改pet成功
                if($uptImg){
                    // 删除现在的图片
                    unlink('./public/upload/'.$big_photo);
                    unlink('./public/upload/'.$small_photo);
                }                
                $this->assign('back_info','success');
            } else {
                $this->assign('back_info','Network error, Please refresh and try again!');
            }
            $this -> redirect('User/userCenter');
        } else {
            if($this->checkPetUser($_GET['id'])==-1){
                $this -> redirect('User/userCenter');
                return;
            }
            // 加载breed信息
            $breedModel = M("Breed");
            $listBreed = $breedModel->select();
            $this->assign('listBreed', $listBreed);
            // 获取当前Pet信息
            $petModel = M('Pet');
            $pet = $petModel->where('pet_id='.$_GET['id'])->find();
            $this->assign('pet',$pet);
            $this->display('uptPet');
        }
    }

    private function checkPetUser($pet_id){
        $userModel = M('User');
        $user = $userModel->where('user_id='.session('user'))->find();
        return strpos($user['pet_ids'],$pet_id);
    }

    public function delete(){
        R('PublicInfo/headerFooter');
        $data='error';
        if(!(session('user')==null || session('user')=='')
            && !empty($_POST['pet_id'])){

            $userModel = M('User');
            $user = $userModel->where('user_id='.session('user'))->find();
            if(strpos($user['pet_ids'],$_POST['pet_id'])!=-1){
                $petModel = M('Pet');
                $pet = $petModel->where('pet_id='.$_POST['pet_id'])->find();
                // 图片路径
                $big_photo = $pet['pet_big_photo'];
                $small_photo = $pet['pet_small_photo'];
                // 删除user关联的pet
                $pet_ids_old = explode(',', $user['pet_ids']);
                $pet_ids = '';
                foreach ($pet_ids_old as $key => $pet_id) {
                    if($pet_id!=$_POST['pet_id'])
                        if($key==0){
                            $pet_ids = $pet_id;
                        } else {
                            $pet_ids = $pet_ids.','.$pet_id;
                        }
                }
                $user['pet_ids'] = $pet_ids;
                $rst = $userModel->where('user_id='.session('user'))->save($user);
                if($rst){
                    // 删除pet
                    $rst = $petModel->where('pet_id='.$_POST['pet_id'])->delete();
                    if($rst){
                        // 删除图片
                        unlink('./public/upload/'.$big_photo);
                        unlink('./public/upload/'.$small_photo);
                        $data='success';
                    }
                }
            }
        }
        $this->ajaxReturn($data);
    }
}