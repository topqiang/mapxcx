<?php
namespace Teach\Controller;
use Think\Controller;

/**
 * Class IndexController
 * @package Admin\Controller
 */
class IndexController extends BaseController {

    public function _initialize(){
        parent::_initialize();
        $this -> inpic = D('Inpic');
        $this -> jkcv = D('Jkcv');
    }

    public function index(){
        $where['status'] = array('neq',9);
        $inpic = $this -> inpic -> where($where) -> select();
        $whe['tid'] = session('teacid');
        $whe['status'] = 1;
        $time = time()-3*24*60*60;
        $whe['infotime'] = array('gt',date('Y.m.d',$time));
        $jkc = $this -> jkcv -> where( $whe ) -> order('infotime asc') -> select();
        $this -> assign('jkcv',$jkc);
        $this -> assign( 'inpic' , $inpic );
        $this->display('index');
    }


}