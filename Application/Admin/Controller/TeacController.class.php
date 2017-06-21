<?php

namespace Admin\Controller;
use Think\Controller;

/**
 * Class NewsController
 * @package Admin\Controller
 */
class TeacController extends AdminBasicController {
    public function _initialize(){
        $this->checkLogin();
        $this->teac = D('Teac');
    }


    /**
     * 公司新闻列表
     */
    public function teaclist(){
        $parameter = array();
        if($_REQUEST['name']){
            $w['name'] = array('LIKE','%'.$_REQUEST['name'].'%');
            $parameter['name'] = $_REQUEST['name'];
            $this->assign("request",$parameter);
        }
        $w['status'] = array('neq',9);
        $list = $this -> teac -> where($w) -> select();
        $this->assign("list",$list);
        $this->display("teaclist");
    }
    /**
     * 删除新闻
     */
    public function deleteteac(){
        if(empty($_REQUEST['id'])){
            $this->error('您未选择任何操作对象');
        }
        $data['id'] = I('request.id');
        //$data['status'] = 9;
        $upd_res = $this -> teac -> delete($data['id']);
        if($upd_res){
            $this->success('删除操作成功');
        }else{
            $this->error('删除操作失败');
        }
    }

    public function resetpass(){
        $data['id'] = I('request.id');
        if ( $data['id'] ) {
           $data['password'] = md5('123456');
            $upd_res = $this -> teac -> save($data);
            if ($upd_res) {
                apiResponse("success","密码重置成功！"); 
            }else{
                apiResponse("error","密码重置失败！"); 
            }
        }else{
            apiResponse("error","未找到合适对象！");
        }
    }

    /**发布文章*/
    public function addteac(){
        if(!IS_POST){
            $this->display("addteac");
        }else{
            if(empty($_FILES['pic']['name'])){
                $this->error("请上传教练头像！");
            }
            if($_FILES['pic']['name']){
                $img = $this->uploadImg("tech","pic");
                $data['headpic'] = 'Uploads/'.$img;
            }
            $data['account'] = $_POST['account'];
            $data['desc'] = $_POST['desc'];
            $data['name'] = $_POST['name'];
            $data['sex'] = $_POST['sex'];
            $data['age'] = $_POST['age'];
            $data['status'] = 0;
            $data['password'] = md5('123456');
            $data['ctime'] = time();
            $data['utime'] = time();
            $res = $this -> teac -> add($data);
            if($res){
                $this->success("添加成功！",U('Teac/teaclist'));
            }else{
                $this->error("添加失败！");
            }
        }
    }

    /**文章修改*/
    public function editteac(){
        if(!IS_POST){
            $w['id'] = $_GET['id'];
            $res = $this -> teac -> where($w) -> find();
            $this->assign("res",$res);
            $this->display("editteac");
        }else{
            $data['id'] = $_POST['id'];
            if($_FILES['pic']['name']){
                $img = $this->uploadImg("tech","pic");
                $data['headpic'] = 'Uploads/'.$img;
            }
            $data['account'] = $_POST['account'];
            $data['desc'] = $_POST['desc'];
            $data['name'] = $_POST['name'];
            $data['sex'] = $_POST['sex'];
            $data['age'] = $_POST['age'];
            $data['utime'] = time();
            $res = $this -> teac ->save($data);
            if($res){
                $this->success("修改成功！",U('Teac/teaclist'));
            }else{
                $this->error("修改失败！");
            }
        }
    }
}