<?php
namespace Api\Controller;
use Think\Controller;
class ReportController extends Controller {
	public function _initialize(){
		$this -> report = M('Report');
	}
	//用户ID，查询用户信息
	public function getUserInfo(){
		$con['id']=$_POST['uid'];
		$userinfo = M('user')->where($con)->find();
		if (!empty($userinfo)) {
			apiResponse("success","查询成功！",$result['result']['data']);
		}else{
			apiResponse("error","查询失败！");
		}
	}

	public function setReport(){
		$data['uid'] = $_POST['uid'];
		$data['lat'] = $_POST['lat'];
		$data['lnt'] = $_POST['lnt'];
		$data['pic'] = $_POST['pic'];
		$data['content'] = $_POST['content'];
		$data['ctime'] = time();
		$data['utime'] = time();
		$data['status'] = 0;

		$res = $this -> report -> add( $data );

		if (!empty($res)) {
			apiResponse("success","等待审核！",$data);
		}else{
			apiResponse("error","上传失败！");
		}
	}

}