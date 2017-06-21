<?php
namespace Teach\Controller;
use Think\Controller;

/**
 * Class IndexController
 * @package Admin\Controller
 */
class JkcController extends BaseController {

    public function _initialize(){
        parent::_initialize();
        $this -> jkcv = D('Jkcv');
        $this -> jkc = D('Jkc');
        $this -> kc = D('kc');
        $this -> inpic = D('Inpic');
        $this -> house = D('House');
    }

    public function kclist(){
        $where['status'] = array('neq',9);
        $res = $this -> kc -> where( $where ) -> select();
        $this -> assign('res',$res);
        $this -> display();
    }

    public function addjkc(){
        $where['status'] = array('neq',9);
        $house = $this -> house -> where( $where ) -> select();
        $week = array();
        $time = time()+24*60*60;
        for ($i=0; $i <7 ; $i++) { 
            $obj['month'] = date('m',$time);
            $obj['day'] = date('d',$time);
            $obj['week'] = $this -> getWeek(date('w',$time));
            $obj['time'] = $time;
            $time += (24*60*60);
            array_push($week,$obj);
        }
        $kid = $_GET['kid'];
        $kcobj = $this -> kc -> where("id=$kid") -> find();
        $this -> assign("kc",$kcobj);
        $this -> assign("week",$week);
        $this -> assign("house",$house);
        $this -> display();
    }

    public function addkc(){
        $data['tid'] = session("teacid");
        $obj = $_POST['obj'];
        $data['kid'] = $_POST['kid'];
        $this -> jkc -> startTrans();
        $flag = false;
        foreach ($obj as $index => $value) {
            $data['week'] = "周".$this -> getWeek(date('w',$index));
            $data['datestr'] = date('m月d日',$index);
            $data['infotime'] = date('Y.m.d',$index);

            foreach ($value as $key => $val) {
                $data['htime'] = $val;
                $data['status'] = 1;
                $res = $this -> jkc -> add( $data );
                if ( empty( $res ) ) {
                    $flag = true;
                }
            }
        }
        if ( $flag ) {
            $this -> jkc -> rollback();
            apiResponse("error","课程审核提交失败！");
        }else{
            $this -> jkc -> commit();
            apiResponse("success","课程审核提交成功！");

        }
    }

    

    public function getWeek($week){
        switch ($week) {
            case '0':
                return "日";
            case '1':
                return "一";
            case '2':
                return "二";
            case '3':
                return "三";
            case '4':
                return "四";
            case '5':
                return "五";
            case '6':
                return "六";
        }
    }

    public function index(){
        $where['status'] = array('neq',9);
        $inpic = $this -> inpic -> where($where) -> select();
        $this -> assign( 'inpic' , $inpic );
        $jkcv = $this -> jkcv -> where( $where ) -> select();
        $this -> assign( 'jkcv' , $jkcv );

        $this->display('index');
    }

    public function kcinfo(){
        $where['id'] = $_GET['kid'];
        $res = $this -> kc -> where( $where ) -> find();
        $this -> assign('res',$res);
        $this -> display();
    }

}