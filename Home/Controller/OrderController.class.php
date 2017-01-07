<?php
namespace Home\Controller;
use Think\Controller;
class OrderController extends Controller {
    
    // 获取head上的联系方式及foot
    public function order(){
        $data = 404; //表示非法请求
        if(!(session('user')==null || session('user')=='')){
            // 查询相应的beg记录
            $begModel = M('Beg');
            $beg = $begModel->where('beg_id='.$_POST['beg_id'])->find();
            if(!empty($beg)){
                // 查询borrower
                $borrowerModel = M('Borrower');
                $borrower = $borrowerModel->where('user_id='.session('user'))->find();
                // 判断用户信息是否完善
                if(empty($borrower) || empty($borrower['borrower_photo']) || empty($borrower['borrower_profile'])){
                    $data = 2; //信息不完善
                } else {
                    // 查询该任务是否已经被接收
                    $orderModel = M('Order');
                    $order = $orderModel->where('beg_id='.$_POST['beg_id'])->find();
                    if($order['order_work']=='0'){
                        $data = 0; //0-processing
                    } elseif ($order['order_work']=='1') {
                        $data = 1; //1-done
                    } else {
                        // 新建order，添加记录
                        $order['beg_id'] = $_POST['beg_id'];
                        $order['borrower_id'] = $borrower['borrower_id'];
                        $order['order_work'] = 0;
                        $order['order_create'] = date('y-m-d h:i:s',time());
                        $rst = $orderModel->add($order);
                        if($rst){
                            $data = 9;//表示成功
                        } else {
                            $data = 10; //添加失败，充实
                        }
                    }
                }
            }
        }
        $this->ajaxReturn($data);
    }

    public function completeOrder(){
        $data = 404; //表示非法请求
        if(!(session('user')==null || session('user')=='')){
            // 查询相应的order记录
            $orderModel = M('Order');
            $order = $orderModel->where('order_id='.$_POST['order_id'])->find();
            if(!empty($order)){
                // 查询当前登录user
                $userLoginModel = M("User_login");
                $userLogin = $userLoginModel->where('user_id='.session('user'))->find();
                if($order['order_work']=='0'){    //二者都未确认
                    if($userLogin['user_type']==1){
                        $order['order_work'] = '3';
                    } else if($userLogin['user_type']==2){
                        $order['order_work'] = '2';
                    }
                } else if($order['order_work']=='2' && $userLogin['user_type']==1){ //Borrower确认,Owner 待确认
                    $order['order_work'] = '1';
                    // borrower count 信息修改
                    $borrowerModel = M('Borrower');
                    $borrower = $borrowerModel->where('borrower_id='.$order['borrower_id'])->find();
                    $borrower['borrower_count'] = $borrower['borrower_count']+1 ;
                    $borrowerModel->where('borrower_id='.$borrower['borrower_id'])->save($borrower);
                } else if($order['order_work']=='3' && $userLogin['user_type']==2){ //Owner确认, Borrower待确认
                    $order['order_work'] = '1';
                    // borrower count 信息修改
                    $borrowerModel = M('Borrower');
                    $borrower = $borrowerModel->where('user_id='.session('user'))->find();
                    $borrower['borrower_count'] = $borrower['borrower_count']+1 ;
                    $borrowerModel->where('borrower_id='.$borrower['borrower_id'])->save($borrower);
                }
                $rst = $orderModel->where('order_id='.$order['order_id'])->save($order);
                if($rst){
                    $data = 1;
                } else {
                    $data = 0;
                }
            }
        }
        $this->ajaxReturn($data);
    }

}