<?php

namespace Home\Controller;
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
        $this->buykc = D('Buykc');
        $this -> jkc = D('Jkc');

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

        $order = $this -> order -> where("id=".$data['id']) -> find();
        $wh['id'] = $order['kid'];
        $res = $this -> jkc -> where( $wh ) -> find();
        $w['kid'] = $res['kid'];
        $w['tid'] = $order['tid'];
        $w['uid'] = $order['uid'];
        $res1 = $this -> buykc -> where( $w ) -> find();

        if ($data['status'] == 1) {
            
            if ($res1) {
                if ($res1['num'] <= $res1['ordernum']) {
                    apiResponse("error","该用户课已上满!");
                }else{
                    $data1['ordernum'] = $res1['ordernum'] + 1;
                    if ( $res1['num'] <= $data1['ordernum'] ) {
                        $data1['status'] = 9;
                    }
                    $this -> buykc ->where('id='.$res1['id'])->save( $data1 );
                }
            }else{
                apiResponse("error","该客户尚未购买该课程！");
            }
        }else if ($data['status'] == 4) {

            $this -> buykc ->where('id='.$res1['id'])->setInc('ordernum',-1);
        
        }
        $res = $this -> order -> save($data);
        if ( $res ) {
            apiResponse("success","修改成功！");
        }else{
            apiResponse("error","修改失败！");
        }
    }
}