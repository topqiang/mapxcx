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
        $this -> buykc = D('Buykc');
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
        $wh['id'] = $_POST['kid'];
        $res = $this -> jkc -> where( $wh ) -> find();
        $w['kid'] = $res['kid'];
        $w['tid'] = $_POST['tid'];
        $w['uid'] = session("userid");
        $ishas = $this -> buykc -> where( $w ) -> find();
        if ($ishas) {
            if ($ishas['num'] == $ishas['ordernum']) {
                apiResponse("error","您的课程已经上完！");
            }
        }else{
            apiResponse("error","您尚未报名此课程！");
        }
        $isplay = $this -> order -> where($data) -> find();
        if ($isplay) {
            apiResponse("error","该课程已被人预约！");
        }
        $data['uid']= session("userid");
        $data['ctime'] = time();
        $data['status'] = 0;
        $res1 = $this -> order -> add($data);
        if ($res1) {
            apiResponse("success","审核提交成功，请等待结果！");
        }else{
            apiResponse("error","预约失败！");
        }

    }

}