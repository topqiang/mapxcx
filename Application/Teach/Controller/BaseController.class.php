<?php

namespace Teach\Controller;
use Think\Controller;

/**
 * Class AdminBasicController
 * @package Admin\Controller
 * 父类  添加登陆验证  权限等
 */
class BaseController extends Controller {

    /**
     * 初始化
     */
    public function _initialize(){
        $tid = session("teacid");
        if ( empty($tid) ) {
            $this-> redirect('Teac/login');
        }
    }


    /**
     * ajax返回数据
     * 2014-6-7
     */
    public function ajaxMsg($f,$m){
        $msg[$f] = $m;
        $this->ajaxReturn($msg,Json);
    }

    /**
     * 分页配置信息
     */
    public function getPageNumber(){
        $page_number = D('Config')->where(array('conf_id'=>1))->getField('page_number');
        return $page_number;
    }

    /**
     * @param $data
     * @param $code
     * @param $pram
     * @return bool
     * 发送短信
     */
    protected function _sendMsg($data, $code, $pram) {
        //获取发信模板
        $tpl = M('SendTemplates')->where(array('code'=>$code))->find();
        //赋值
        foreach($pram as $k => $p) {
            $tpl['content'] = preg_replace("/{".$k."}/i",$p,$tpl['content']);
        }
        //发送短息
        $r = D('SendMsg','Service')->sendMsg($data['mobile'],$tpl['content']);
        //创建发信记录参数
        $data_1 = array(
            'm_id'          => $data['m_id'],
            'way'           => $data['mobile'],
            'send_type'     => 2,
            'content'       => $tpl['content'],
            'template_id'   => $tpl['template_id'],
            'ctime'         => time()
        );
        if(empty($r['error'])) {
            //添加发信记录
            $data_1['status'] = 1;
            M('SendLog')->data($data_1)->add();
            return true;
        } else {
            M('SendLog')->data($data_1)->add();
            return false;
        }
    }

    /**
     * 添加行为日志
     */
    protected function addActionLog($title = '', $table_name = '', $record_id = 0, $remark = '') {
        $data['a_id']       = session('A_ID');
        $data['account']    = session('A_ACCOUNT');
        $data['title']      = $title;
        $data['table_name'] = C('DB_PREFIX').$table_name;
        $data['record_id']  = $record_id;
        $data['remark']     = empty($remark) ? '操作url：'.$_SERVER['REQUEST_URI'] : $remark;
        $data['ctime']      = time();

        $r = M('ActionLog')->data($data)->add();

        if($r) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 单图片上传图片
     */
    public function uploadImg($file,$name){
        $config = array(
            'subName'    =>    array('date','Ym'), //设置文件名
        );
        $upload = new \Think\Upload($config);// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->savePath  =      "$file/"; // 设置附件上传目录    // 上传文件
        $info   =   $upload->upload();
        $savename = $info[$name]['savename'];
        $savepath = $info[$name]['savepath'];
        $a =$savepath.$savename;
        return $a;
    }
    /**
     * 上传图片
     */
    public function uploadImgMore($file){
        $config = array(
            'subName'    =>    array('date','Ym'), //设置文件名
        );
        $upload = new \Think\Upload($config);// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->savePath  =      "$file/"; // 设置附件上传目录    // 上传文件
        $info   =   $upload->upload();
        return $info;
    }


    /**
     * 获取接口信息，这个是万能的调用微信api方法的函数，可以自定的加载url，然后返回你想得到的数据
     */
    public function httpsRequest($url,$data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    /**获取access_token，封装好的一个方法，可以利用缓存，减少我们调用的频次*/
    function wx_get_token() {
        $token = S('access_token');
        if (!$token) {
            $appid = "wxea15d2ffd2851a42";
            $appsecret1 = "12a9948b3ecc2099c7ea5ecbff200379";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret1";
            $output = $this->httpsRequest($url);
            $res = json_decode($output, true);
            $token = $res['access_token'];
            // 注意：这里需要将获取到的token缓存起来（或写到数据库中）
            // 不能频繁的访问https://api.weixin.qq.com/cgi-bin/token，每日有次数限制
            // 通过此接口返回的token的有效期目前为2小时。令牌失效后，JS-SDK也就不能用了。
            // 因此，这里将token值缓存1小时，比2小时小。缓存失效后，再从接口获取新的token，这样
            // 就可以避免token失效。
            // S()是ThinkPhp的缓存函数，如果使用的是不ThinkPhp框架，可以使用你的缓存函数，或使用数据库来保存。
            S('access_token', $token, 3600);
        }
        return $token;
    }
}