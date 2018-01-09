<?php

namespace Admin\Controller;
use Think\Controller;

/**
 * Class NewsController
 * @package Admin\Controller
 */
class InpicController extends AdminBasicController {
    public $Article = '';
    public function _initialize(){
        $this->checkLogin();
        $this->inpic = D('Inpic');
    }


    /**
     * 公司新闻列表
     */
    public function inpiclist(){
        $w['status'] = array('neq',9);
        $list = $this -> inpic -> where($w) ->select();
        $this->assign("list",$list);
        $this->display();
    }
    /**
     * 删除新闻
     */
    public function deleteinpic(){

        if(empty($_REQUEST['id'])){
            $this->error('您未选择任何操作对象');
        }
        $data['id'] = array('IN',I('request.id'));
        $data['status'] = 9;
        $upd_res = $this -> inpic -> save( $data );
        
        if($upd_res){
            $this->success('删除操作成功');
        }else{
            $this->error('删除操作失败');
        }
    }

    /**发布文章*/
    public function addinpic(){
        if(!IS_POST){
            $this->display("addinpic");
        }else{
            if(empty($_FILES['pic']['name'])){
                $this->error("请上传轮播图片！");
            }else{
                $img = $this->uploadImg("inpic","pic");
                $data['pic'] = $img;
                $data['desc'] = $_POST['desc'];
                $data['href'] = $_POST['href'];
                $data['status'] = 0;
                $data['ctime'] = time();
                $res = $this -> inpic ->add($data);
                if($res){
                    $this->success("添加成功！",U('Inpic/inpiclist'));
                }else{
                    $this->error("添加失败！");
                }
            }
        }
    }



}