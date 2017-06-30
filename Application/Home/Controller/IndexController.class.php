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
        $this -> buykc = D('Buykc');

    }

    public function index(){
        $where['status'] = array('neq',9);
        $inpic = $this -> inpic -> where($where) -> select();
        $whe['status'] = 1;
        $time = time();
        $uid = session('userid');
        
        $res = $this -> buykc -> where( "uid=$uid" ) -> field('tid,kid') -> distinct(true) ->select();
        $tid = array();
        $kid = array();
        foreach ($res as $key => $value) {
            array_push($tid,$value['tid']);
            array_push($kid,$value['kid']);
        }
        $whe['tid'] = array('in',$tid);
        $whe['kid'] = array('in',$kid);
        $whe['status'] = array('neq',9);


        $whe['infotime'] = array('gt',date('Y.m.d',$time));
        $jkc = $this -> jkcv -> where( $whe ) -> order('infotime asc') -> select();
        // echo $this -> jkcv -> getLastsql();
        // exit();
        $this -> assign('jkcv',$jkc);
        $this -> assign( 'inpic' , $inpic );
        $this->display('index');
    }

}