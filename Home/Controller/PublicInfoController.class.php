<?php
namespace Home\Controller;
use Think\Controller;
class PublicInfoController extends Controller {
    
    // 获取head上的联系方式及foot
    public function headerFooter(){
        // 读取联系方式
        $index_model = M("index");
        $rst = $index_model -> find();
        // 把数据assign到模板
        $this -> assign('index_phone', $rst['index_phone']);
        // 获取foot部分的内容
        $case_model = new \Think\Model();
        $sql = "SELECT * FROM pet_case ORDER BY case_time DESC, case_id DESC LIMIT 3";
        $rst = $case_model -> query($sql);
        $this -> assign('case_info', $rst);
    }

}