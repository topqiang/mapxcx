<?php
namespace Home\Controller;
use Think\Controller;
class HomeBasicController extends Controller{
    public function _initialize(){
        // if ( empty(session("userid")) ) {
        //     session("userid",1);
        // }
        $user = session("userid");
        $this -> appid = "wxd94e1eeb42f05643";
        $this -> scret = "1d197dc0b61035ce7015b2a3e27ee144";
        $this -> baseurl = "http://jians.91fluid.com";
        $redirect_uri = $this -> baseurl.$_SERVER['REQUEST_URI'];
        $isweixin = preg_match('/MicroMessenger/',$_SERVER['HTTP_USER_AGENT']);
        $state = $_REQUEST['state'];
        $code = $_REQUEST['code'];
        if ($state && empty($user)) {
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this -> appid."&secret=".$this -> scret."&code=$code&grant_type=authorization_code";
            $res = $this -> curl("",$url);
            $access = json_decode($res,true);
            S('access_token',$access['access_token'],2*60*40);
            S('refresh_token',$access['refresh_token']);
            session('openid',$access['openid']);
            $where['wx_id'] = session('openid');
            $muser = D('User');
            $userobj = $muser -> where($where) -> select();
            if ($userobj) {
                session('userid',$userobj[0]['id']);
                session('username',$userobj[0]['name']);

            }else{
                $userobj = $this -> getUserInfo($access['openid'],$access['access_token']);
                session('userid',$userobj['id']);
                $data['nick_name'] = $userobj['nickname'];
                $data['provance'] = $userobj['province'].$userobj['city'];
                $data['status'] = 0;
                $data['wx_id'] = $userobj['openid'];
                $data['headpic'] = $userobj['headimgurl'];
                $data['ctime'] = time();
                $muser -> add($data);
            }
        }else if ( empty($user) ) {
            $code = session('code');
            if (!isset($code)) {
                $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appid."&redirect_uri=".urlencode($redirect_uri)."&response_type=code&scope=snsapi_userinfo&state=weixin#wechat_redirect";
                Header("Location: $url");
            }
        }
        $new = new \Think\Jssdk($this -> appid,$this -> scret);
        $return = $new->getSignPackage();
        $this->assign('parameter',$return);
    }

    public function getUserInfo($openid,$access_token){
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
        $res = $this -> curl("",$url,"GET");
        $access = json_decode($res,true);
        return $access;
    }

    public function getAccess(){
        $access = S('access_token');
        if ( $access ) {
            return $access;
        }else{
            $refresh_token = S('refresh_token');
            $url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=".$this -> appid."&grant_type=refresh_token&refresh_token=$refresh_token";
            $res = $this -> curl("",$url);
            $access = json_decode($res,true);
            S('access_token',$access['access_token'],2*60*60);
            S('refresh_token',$access['refresh_token']);
            return $access['access_token'];
        }
        
    }

    public function curl($data,$url,$type="POST"){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT,  'Mozilla/5.0 (compatible;MSIE 5.01;Windows NT5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo=curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_errno($ch);
        }
        curl_close($ch);
        return $tmpInfo;
    }
}