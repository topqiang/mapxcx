<?php
namespace Teach\Controller;
use Think\Controller;

/**
 * Class IndexController
 * @package Admin\Controller
 */
class TeacController extends BaseController {

    public function _initialize(){
        $this -> teac = D("Teac");
        $this -> jkcv = D('Jkcv');
        $this -> jkc = D('Jkc');

        $this->order = D('Order');
        $this->orderv = D('Orderv');
        $this->buykc = D('Buykc');
    }

    public function login(){
        $this ->  display();
    }

    public function setpro(){
        $val = $_POST['val'];
        $key = $_POST['key'];
        $pic = $_POST['pic'];
        $pic_name = $_POST['pic_name'];

        if ($val && $key) {
            $data = array(
                'id' => session("teacid"),
                $key => $val,
                'utime' => time()
                );
            $res = $this -> teac -> save($data);
            if ($res) {
                apiResponse("success","修改成功！");   
            }else{
                apiResponse("error","修改失败！");
            }
        }elseif ( $pic && $pic_name ) {
            $res1 = uploadPic($pic,$pic_name);
            if ($res1 == "error") {
                apiResponse("error","上传失败！");
            }else{
                $data = array(
                    'id' => session("teacid"),
                    'headpic' => $res1,
                    'utime' => time()
                    );
                $res = $this -> teac -> save($data);
                if ($res) {
                    apiResponse("success","修改成功！",$res1);   
                }else{
                    apiResponse("error","修改失败！");
                }
            }
        }else{
            apiResponse("error","请求无效！");
        }
    }

    public function userorder(){
        $id = $_GET['oid'];
        $user = $this -> orderv -> where( "id=$id" ) -> find();
        $this -> assign('user',$user);
        $this -> display();
    }

    public function myuser(){
        $where['tid'] = session('teacid');
        $time = date("Y.m.d",time());
        $where['infotime'] = array('egt',$time);
        $res = $this -> orderv -> where($where) -> order('infotime asc') -> select();
        $this -> assign('userlist',$res);
        $this -> display();
    }

    public function log(){
    	$account = $_POST['account'];
    	$password = $_POST['password'];

    	if ($account && $password) {
    		$where['account'] = $account;
    		$res = $this -> teac -> where($where) -> find();
    		if ( $res ) {
    			if (md5($password) == $res['password']) {
    				session("teacid",$res['id']);
    				apiResponse("success","登录成功！");
    			}else{
    				apiResponse('error','密码不正确！');
    			}
    		}else{
    			apiResponse("error","用户不存在！");
    		}
    	}else{
    		apiResponse("error","手机号密码不能为空！");
    	}
    }


    public function self(){
        $where['id'] = session('teacid');
        $res = $this -> teac -> where( $where ) -> find();
        $whe['tid'] = $where['id'];
        $time = time()-3*24*60*60;
        $whe['infotime'] = array('gt',date('Y.m.d',$time));
        $jkc = $this -> jkcv -> where( $whe ) -> order('infotime asc') -> select();
        $this -> assign('jkc',$jkc);
        $this -> assign('teac',$res);
        $this -> display();
    }

    public function selfinfo(){
        $where['id'] = session('teacid');
        $res = $this -> teac -> where( $where ) -> find();
        $this -> assign('teac',$res);
        $this -> display();
    }

    public function regist(){
    	$account = $_POST['account'];
    	$password = $_POST['password'];
    	if ($account && $password) {
    		$data['account'] = $account;
            $res1 = $this -> teac -> where($data) -> find();
            if ($res1) {
                apiResponse("error","用户已存在！");
            }
    		$data['password'] = md5($password);
            $data['name'] = $account;
            $data['sex'] = 1;
            $data['status'] = 1;
            $data['ctime'] = time();
            $data['utime'] = time();
    		$res = $this -> teac -> add($data);
    		if ( $res ) {
    			session("teacid",$res);
    			apiResponse("success","注册成功！");
    		}
    	}else{
    		apiResponse("error","手机号密码不能为空！");
    	}
    }

    public function cancelorder(){
        $data['id'] = $_POST['id'];
        $data['status'] = $_POST['status'];
        if ($data['status'] == 1) {
            $order = $this -> order -> where("id=".$data['id']) -> find();
            $wh['id'] = $order['kid'];
            $res = $this -> jkc -> where( $wh ) -> find();
            $w['kid'] = $res['kid'];
            $w['tid'] = $order['tid'];
            $w['uid'] = $order['uid'];
            $res1 = $this -> buykc -> where( $w ) -> find();
            if ($res1) {
                if ($res1['num'] <= $res1['ordernum']) {
                    apiResponse("error","该用户课已上满");
                }else{
                    $data1['ordernum'] = $res1['ordernum'] + 1;
                    if ( $res1['num'] <= $data1['ordernum'] ) {
                        $data1['status'] = 9;
                    }
                    $this -> buykc ->where('id='.$res1['id'])->save( $data1 );
                }
            }else{
                apiResponse("error","该客户尚未购买该课程！");
            }
        }
        $res = $this -> order -> save($data);
        if ( $res ) {
            apiResponse("success","修改成功！");
        }else{
            apiResponse("error","修改失败！");
        }
    }
}