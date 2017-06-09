<?php
namespace Home\Controller;
use Think\Controller;

/**
 * Class IndexController
 * @package Admin\Controller
 */
class UserController extends HomeBasicController {

    public function _initialize(){
        parent::_initialize();
        $this -> order = D('Order');
        $this -> user = D('User');

    }

    public function selfinfo(){
        $id = session("userid");
        $res = $this -> user -> where("id = $id") -> find();
        $this -> assign('user',$res);
        $this -> display();
    }

    public function self(){
        $id = session("userid");
        $res = $this -> user -> where("id = $id") -> find();
        $order = $this -> order -> where("uid=$id and status <> 9") -> order("ctime desc") -> select();
        $this -> assign('orderlist',$order);
        $this -> assign('user',$res);
        $this -> display();
    }

    public function setpro(){
        $val = $_POST['val'];
        $key = $_POST['key'];
        $pic = $_POST['pic'];
        $pic_name = $_POST['pic_name'];

        if ($val && $key) {
            $data = array(
                'id' => session("userid"),
                $key => $val,
                'utime' => time()
                );
            $res = $this -> user -> save($data);
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
                    'id' => session("userid"),
                    'headpic' => $res1,
                    'utime' => time()
                    );
                $res = $this -> user -> save($data);
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

    public function cancelorder(){

        $data['id'] = $_POST['oid'];
        $data['status'] = 4;

        $res = $this -> order -> save($data);
        if ( $res ) {
            apiResponse("success","取消成功！");
        }else{
            apiResponse("error","取消失败！");
        }
    }

}