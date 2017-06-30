<?php

namespace Admin\Controller;
use Think\Controller;

/**
 * Class NewsController
 * @package Admin\Controller
 */
class KcController extends AdminBasicController {
    public $Article = '';
    public function _initialize(){
        $this->checkLogin();
        $this->kc = D('Kc');
        $this->jkcv = D('Jkcv');
        $this -> jkc = D('Jkc');

        $this -> hou = D('House');
    }

    public function savekc(){
        $id = $_POST['id'];
        $status = $_POST['status'];
        $arrdata = array(
            'id' => $id,
            'status' => $status
            );
        $res = $this -> jkc -> save( $arrdata );
        if ( $res ) {
            apiResponse("success","修改成功！");
        }else{
            apiResponse("error","修改失败！");
        }
    }
    /**
     * 公司新闻列表
     */
    public function kclist(){
        $w['status'] = array('neq',9);
        $list = $this -> kc -> where($w) ->select();
        $this->assign("list",$list);
        $this->display();
    }
    /**
     * 删除新闻
     */
    public function deletekc(){

        if(empty($_REQUEST['id'])){
            $this->error('您未选择任何操作对象');
        }
        $data['id'] = array('IN',I('request.id'));
        $data['status'] = 9;
        $upd_res = $this -> kc -> save( $data );
        
        if($upd_res){
            $this->success('删除操作成功');
        }else{
            $this->error('删除操作失败');
        }
    }

    public function deletejkc(){

        if(empty($_REQUEST['id'])){
            $this->error('您未选择任何操作对象');
        }
        $data['id'] = array('IN',I('request.id'));
        $data['status'] = 9;
        $upd_res = $this -> jkc -> save( $data );
        
        if($upd_res){
            $this->success('删除操作成功');
        }else{
            $this->error('删除操作失败');
        }
    }

    public function addkc(){
        if(!IS_POST){
            $this->display("addkc");
        }else{
            if(empty($_FILES['pic']['name'])){
                $this->error("请上传轮播图片！");
            }else{
                $img = $this->uploadImg("inpic","pic");
                $data['pic'] = $img;
                $data['desc'] = $_POST['desc'];
                $data['name'] = $_POST['name'];
                $data['ktime'] = $_POST['ktime'];
                $data['status'] = 0;
                $data['ctime'] = time();
                $data['utime'] = time();
                $res = $this -> kc ->add($data);
                if($res){
                    $this->success("添加成功！",U('Kc/kclist'));
                }else{
                    $this->error("添加失败！");
                }
            }
        }
    }

    public function jkclist(){
        $name = $_POST['name'];
        $tname = $_POST['tname'];

        $status = $_POST['status'];
        if ($name) {
            $where['name'] = array("like","%$name%");
        }
        if ($tname) {
            $where['tname'] = array("like","%$tname%");
        }
        $where['status'] = array('neq',9);
        if ($status) {
            $where['status'] = $status;
        }
        $res = $this -> jkcv -> where( $where ) -> order('infotime desc') -> select();
        $this -> assign('list',$res);
        $this -> display();
    }

    /**修改文章*/
    public function editkc(){
        if(!IS_POST){
            $w['id'] = $_GET['id'];
            $res = $this -> kc -> where($w) -> find();
            $this->assign("res",$res);
            $this->display("editkc");
        }else{
            if( $_POST['id'] ){
                $data['id'] = $_POST['id'];
            }else{
                $this->error("修改失败！");
            }
            if(!empty($_FILES['pic']['name'])){
                $img = $this->uploadImg("inpic","pic");
                $data['pic'] = $img;
            }
            if( $_POST['desc'] ){
                $data['desc'] = $_POST['desc'];
            }
            if( $_POST['name'] ){
                $data['name'] = $_POST['name'];
            }
            if( $_POST['ktime'] ){
                $data['ktime'] = $_POST['ktime'];
            }
            $data['utime'] = time();
            $res = $this -> kc -> save($data);
            if($res){
                $this->success("修改成功！",U('Kc/kclist'));
            }else{
                $this->error("修改失败！");
            }
        }
    }

    public function houlist(){
        $w['status'] = array('neq',9);
        $res = $this -> hou -> where($w) ->select();
        $this -> assign('list',$res);
        $this -> display();
    }

    public function addhou(){
        $data['name'] = $_POST['name'];
        $data['remark'] = $_POST['remark'];
        if ( empty($data['name']) ) {
            apiResponse("error","时段不能为空！");
        }
        $data['status'] = 0;
        $res = $this -> hou -> add($data);
        if ( $res ) {
            apiResponse("success","保存成功！");
        }else{
            apiResponse("error","保存失败！");
        }
    }

    public function deletehou(){
        if(empty($_REQUEST['id'])){
            $this->error('您未选择任何操作对象');
        }
        $data['id'] = array('IN',I('request.id'));
        $data['status'] = 9;
        $upd_res = $this -> hou -> save( $data );
        if($upd_res){
            $this->success('删除操作成功');
        }else{
            $this->error('删除操作失败');
        }
    }

}