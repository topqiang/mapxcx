<?php

namespace Admin\Controller;
use Think\Controller;

/**
 * Class NewsController
 * @package Admin\Controller
 */
class VorderController extends AdminBasicController {
    public function _initialize(){
        $this->checkLogin();
        $this->order = D('Order');
        $this->orderv = D('Orderv');

    }


    /**
     * 公司新闻列表
     */
    public function vorderlist(){
        if($_REQUEST['kname']){
            $w['kname'] = array('LIKE','%'.$_REQUEST['kname'].'%');
        }
        if($_REQUEST['tname']){
            $w['tname'] = array('LIKE','%'.$_REQUEST['tname'].'%');
        }
        if($_REQUEST['tel']){
            $w['tel'] = $_REQUEST['tel'];
        }
        $w['status'] = array('neq',9);
        $list = $this -> orderv -> where($w) -> order('ctime desc') -> select();
        $this->assign("list",$list);
        $this->display();
    }
    /**
     * 删除新闻
     */
    public function cancelorder(){

        $data['id'] = $_POST['id'];
        $data['status'] = $_POST['status'];

        $res = $this -> order -> save($data);
        if ( $res ) {
            apiResponse("success","修改成功！");
        }else{
            apiResponse("error","修改失败！");
        }
    }
}