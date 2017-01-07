<?php
namespace Home\Controller;
use Think\Controller;
class CaseController extends Controller {
    
    public function showCase(){
        $caseModel = M("Case");
        // 获取当前记录总条数
        $total = $caseModel->count();
        $per = 3;   // 每页显示数
        // 实例化分页类对象
        $page = new \Component\Page($total, $per);
        // 拼装sql语句获得每页信息
        $sql = "select * from pet_case order by case_time desc ".$page->limit;
        $info = $caseModel -> query($sql);
        // 处理时间
        $size = count($info);
        for($i=0; $i<$size; $i++){
            $caselist[$i]['year'] = date('Y', strtotime($info[$i]['case_time']));
            $caselist[$i]['month'] = date('M', strtotime($info[$i]['case_time']));
            $caselist[$i]['day'] = date('d', strtotime($info[$i]['case_time']));
            $caselist[$i]['case'] = $info[$i];
        }
        $this->assign('caselist', $caselist);
        // 获得页码页表
        $pagelist = $page -> fpage();
        $this -> assign("pagelist", $pagelist);
        // 获取公共信息
        R('PublicInfo/headerFooter');
        R('Beg/getRecentBegs',array('6'));
        $this -> display('case');
    }

    public function search(){
        $caseModel = M("Case");
        if(!empty($_POST)){
            session('search_case_key', $_POST['search_key']);
        }
        // 获取当前记录总条数
        $total = $caseModel->where("case_title like '%".session('search_case_key')."%'")->count();
        $per = 3;   // 每页显示数
        // 实例化分页类对象
        $page = new \Component\Page($total, $per);
        // 拼装sql语句获得每页信息
        $sql = "select * from pet_case where case_title like '%".session('search_case_key')."%' order by case_time desc ".$page->limit;
        $info = $caseModel -> query($sql);
        // 处理时间
        $size = count($info);
        for($i=0; $i<$size; $i++){
            $caselist[$i]['year'] = date('Y', strtotime($info[$i]['case_time']));
            $caselist[$i]['month'] = date('M', strtotime($info[$i]['case_time']));
            $caselist[$i]['day'] = date('d', strtotime($info[$i]['case_time']));
            $caselist[$i]['case'] = $info[$i];
        }
        $this->assign('caselist', $caselist);
        // 获得页码页表
        $pagelist = $page -> fpage();
        $this -> assign("pagelist", $pagelist);
        // 获取公共信息
        R('PublicInfo/headerFooter');
        R('Beg/getRecentBegs',array('6'));
        $this -> display('case');
    }

    public function getCases(){
        $caseModel = M("Case");
        $cases = $caseModel->order('case_time desc')->limit(0,6)->select();
        foreach ($cases as $key => $case) {
            $listCase[$key]['case_small_photo'] = $case['case_small_photo'];
            $listCase[$key]['case_title'] = $case['case_title'];
            if(strlen($case['case_content'])>55){
                $listCase[$key]['case_content'] = substr($case['case_content'],0,55).'...';
            } else {
                $listCase[$key]['case_content'] = $case['case_content'];
            }
        }
        $this -> assign("cases", $listCase);
    }

}