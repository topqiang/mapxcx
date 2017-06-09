<?php
namespace Home\Controller;
use Think\Controller;

/**
 * Class IndexController
 * @package Admin\Controller
 */
class OrderController extends HomeBasicController {

    public function _initialize(){
        parent::_initialize();
        $this -> jkcv = D('Jkcv');
        $this -> jkc = D('Jkc');
        $this -> kc = D('kc');
        $this -> order = D('Order');

    }

    public function addorder(){

        $data = array(
            'kid' => $_POST['kid'],
            'tid' => $_POST['tid'],
            'tname' => $_POST['tname'],
            'ktime' => $_POST['ktime'],
            'kname' => $_POST['kname'],
            'htime' => $_POST['htime'],
            'weekday' => $_POST['weekday'],
            'infotime' => $_POST['infotime']
            );
        $isplay = $this -> order -> where($data) -> find();
        if ($isplay) {
            apiResponse("error","该课程已被人预约！");
        }
        $data['uid']= session("userid");
        $data['ctime'] = time();
        $data['status'] = 0;
        $res1 = $this -> order -> add($data);
        if ($res1) {
            apiResponse("success","预约成功，请按时参加！");
        }else{
            apiResponse("error","预约失败！");
        }

    }

}