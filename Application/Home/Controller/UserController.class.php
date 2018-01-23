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

    /**
     * 用户列表
     */
    public function userlist(){
        $w['status'] = array('neq',9);
        $name = $_REQUEST['nick_name'];
        if($name){
            $w['uname'] = array('LIKE','%'.$_REQUEST['nick_name'].'%');
        }
        $count =  $this -> user -> where($where)->count();
        $page = new \Think\Page($count,15);
        if ($name) {
            $page->parameter['nick_name'] =  $name;
        }
        $userlist = $this -> user -> where($w) -> order('addtime desc') -> limit($page->firstRow,$page->listRows) -> select();
        $this -> assign('list',$userlist);
        $this -> assign('page',$page->show());
        $this->display();
    }
}