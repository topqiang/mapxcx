<?php
namespace Home\Controller;
use Think\Controller;

/**
 * Class IndexController
 * @package Admin\Controller
 */
class JkcController extends HomeBasicController {

    public function _initialize(){
        parent::_initialize();
        $this -> jkcv = D('Jkcv');
        $this -> jkc = D('Jkc');
        $this -> kc = D('kc');
    }

    public function jkcinfo(){
        $id = $_GET['id'];
        $kcobj = $this -> jkcv -> where("id=$id") -> find();
        $this -> assign("kc",$kcobj);
        $this -> display();
    }

}