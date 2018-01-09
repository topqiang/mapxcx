<?php
namespace Api\Controller;
use Think\Controller;
class LoginController extends Controller {
	public function authlogin(){
		$openid = $_POST['openid'];
		if (!$openid) {
			echo json_encode(array('status'=>0,'err'=>'授权失败！'.__LINE__));
			exit();
		}
		$con = array();
		$con['openid']=trim($openid);
		$uid = M('user')->where($con)->getField('id');
		if ($uid) {
			$userinfo = M('user')->where('id='.intval($uid))->find();
			if (intval($userinfo['del'])==1) {
				echo json_encode(array('status'=>0,'err'=>'账号状态异常！'));
				exit();
			}
			$err = array();
			$err['ID'] = intval($uid);
			$err['NickName'] = $_POST['NickName'];
			$err['HeadUrl'] = $_POST['HeadUrl'];
			echo json_encode(array('status'=>1,'arr'=>$err));
			exit();
		}else{
			$data = array();
			$data['name'] = $_POST['NickName'];
			$data['uname'] = $_POST['NickName'];
			$data['photo'] = $_POST['HeadUrl'];
			$data['sex'] = $_POST['gender'];
			$data['pwd'] = md5("123456");
			$data['openid'] = $openid;
			$data['source'] = 'wx';
			$data['addtime'] = time();
			if (!$data['openid']) {
				echo json_encode(array('status'=>0,'err'=>'授权失败！'.__LINE__));
				exit();
			}
			$res = M('user')->add($data);
			if ($res) {
				$err = array();
				$err['ID'] = intval($res);
				$err['NickName'] = $data['name'];
				$err['HeadUrl'] = $data['photo'];
				echo json_encode(array('status'=>1,'arr'=>$err));
				exit();
			}else{
				echo json_encode(array('status'=>0,'err'=>'授权失败！'.__LINE__));
				exit();
			}
		}
	}
}