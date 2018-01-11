<?php

namespace Home\Controller;
use Think\Controller;

/**
 * Class NewsController
 * @package Admin\Controller
 */
class UserController extends AdminBasicController {
    public function _initialize(){
        $this -> checkLogin();
        $this -> user = D('User');

    }

    public function buykc(){
        $uid = $_POST['uid'];
        $kid = $_POST['kid'];
        $tid = $_POST['tid'];
        $num = $_POST['num'];

        $data = array(
            'uid' => $uid,
            'kid' => $kid,
            'tid' => $tid
            );
        $has = $this -> buykc -> where( $data ) -> select();
        if ($has) {
            apiResponse("error","该用户已报过该课程");
        }
        $data['num'] = $num;
        $data['ctime'] = time();
        $data['utime'] = time();
        $res = $this -> buykc -> add( $data );
        if ( $res ) {
           apiResponse("success","添加成功！");
        }else{
            apiResponse("error","添加失败！");
        }
    }

    public function getkc(){
        $tid = $_POST['tid'];
        if (empty($tid)) {
            apiResponse("error","教练信息为空！");
        }
        $w['tid'] = $tid;
        $w['status'] = array( array('neq',9) , array('neq',0) , 'and' );
        $jkc = $this -> jkcv -> distinct( true ) -> where( $w ) -> field('kid,name') -> select();
        if ($jkc) {
            apiResponse("success","查询成功！",$jkc);
        }else{
            apiResponse("error","未查到相关课程！");
        }
    }

    /**
     * 公司新闻列表
     */
    public function userlist(){
        $w['status'] = array('neq',9);
        
        if($_REQUEST['nick_name']){
            $w['uname'] = array('LIKE','%'.$_REQUEST['nick_name'].'%');
        }

        $list = $this -> user -> where($w) -> order('addtime desc') -> select();
        $this->assign("list",$list);
        $this->display();
    }
    /**
     * 删除新闻
     */
    public function canceluser(){

        $data['id'] = $_POST['id'];
        $data['status'] = $_POST['status'];

        $res = $this -> user -> save($data);
        if ( $res ) {
            apiResponse("success","修改成功！");
        }else{
            apiResponse("error","修改失败！");
        }
    }
}