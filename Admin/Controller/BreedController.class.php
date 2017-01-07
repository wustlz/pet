<?php
namespace Admin\Controller;
use Think\Controller;

class BreedController extends Controller {

    public function breedlist(){
        if(empty(session('admin'))){
            $this->display('/Index/sign-in');
        } else {
            $this->loadBreed();
            $this->assign('curMethod','breed');
            $this->display('/Index/breed');
        }
    }

    public function addBreed(){
        if(empty(session('admin'))){
            $this->display('/Index/sign-in');
            return;
        } else if(!empty($_GET['name'])){
            $breedModel = M("Breed");
            $breed['breed_name'] = $_GET['name'];
            $breedModel->add($breed);
        }
        $this->breedlist();
    }

    public function uptBreed(){
        if(empty(session('admin'))){
            $this->display('/Index/sign-in');
            return;
        } else if(!empty($_GET)){
            $breedModel = M("Breed");
            $breed['breed_name'] = $_GET['name'];
            $breedModel->where('breed_id='.$_GET['id'])->save($breed);
        }
        $this->breedlist();
    }

    public function delBreed(){
        if(empty(session('admin'))){
            $this->display('/Index/sign-in');
            return;
        } else if(!empty($_GET['id'])){
            $breedModel = M("Breed");
            $breedModel->where('breed_id='.$_GET['id'])->delete();
        }
        $this->breedlist();
    }

    function loadBreed(){
        $breedModel = M("Breed");
        $listBreed = $breedModel->select();
        $this->assign('listBreed', $listBreed);
    }
}