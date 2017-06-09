<?php
namespace Home\Controller;
use Think\Controller;

/**
 * Class IndexController
 * @package Admin\Controller
 */
class IndexController extends HomeBasicController {

    public function _initialize(){
        parent::_initialize();
        $this -> inpic = D('Inpic');
        $this -> jkcv = D('Jkcv');
    }

    public function index(){
        $where['status'] = array('neq',9);
        $inpic = $this -> inpic -> where($where) -> select();
        $whe['status'] = 1;
        $time = time();
        $whe['infotime'] = array('gt',date('Y.m.d',$time));
        $jkc = $this -> jkcv -> where( $whe ) -> order('infotime asc') -> select();
        $this -> assign('jkcv',$jkc);
        $this -> assign( 'inpic' , $inpic );
        $this->display('index');
    }

}