<?php

namespace Admin\Controller;
use Think\Controller;

/**
 * Class NewsController
 * @package Admin\Controller
 */
class UserController extends AdminBasicController {
    public function _initialize(){
        $this->checkLogin();
        $this->user = D('User');
    }


    /**
     * 公司新闻列表
     */
    public function userlist(){
        if($_REQUEST['nick_name']){
            $w['nick_name'] = array('LIKE','%'.$_REQUEST['nick_name'].'%');
        }
        if($_REQUEST['tel']){
            $w['tel'] = $_REQUEST['tel'];
        }
        $w['status'] = array('neq',9);
        $list = $this -> user -> where($w) -> order('ctime desc') -> select();
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