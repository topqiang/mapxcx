<?php
namespace Api\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function _initialize(){
		$this -> key = "OB4BZ-D4W3U-B7VVO-4PJWW-6TKDJ-WPB77";//"AJ3BZ-EPVCQ-NEY5U-G5H5V-2THSH-XFFI4";
		$this -> searchurl = "https://apis.map.qq.com/ws/place/v1/search?";
	}
	public function search(){
		$data['uid'] = $_POST['uid'];
		$data['latitude'] = $_POST['latitude'];
		$data['longitude'] = $_POST['longitude'];
		$data['keyword'] = $_POST['keyword'];
		$data['ctime'] = time();
		$data['status'] = 0;
		$history = M('history');
		$res = $history -> add( $data );
		$where['keyword'] = urlencode($data['keyword']);
		$where['boundary'] = "nearby(".$data['latitude'].",".$data['longitude'].",1000)";
		$where['key'] = $this -> key;

		if (!empty($_POST['filter'])) {
			$where['filter'] = $_POST['filter'];
		}
		$this -> searchurl = "https://apis.map.qq.com/ws/place/v1/search?keyword=".$where['keyword']."&boundary=nearby(".$data['latitude'].",".$data['longitude'].",1000)&key=".$where['key'];
		//http://apis.map.qq.com/ws/place/v1/search?keyword=%E9%85%92%E5%BA%97&boundary=nearby(39.908491,116.374328,1000)&key=OB4BZ-D4W3U-B7VVO-4PJWW-6TKDJ-WPB77
		//$res1 = file_get_contents($this -> searchurl);

		$res1 = $this -> curl( "" , $this -> searchurl , "get" );
		apiResponse( "success" , "查询成功！" , $res1 );
	}

	public function curl($data,$url,$type="post"){
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "get");
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // curl_setopt($ch, CURLOPT_USERAGENT,  'Mozilla/5.0 (compatible;MSIE 5.01;Windows NT5.0)');
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        // curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // $tmpInfo=curl_exec($ch);
        // if (curl_errno($ch)) {
        //     return curl_errno($ch);
        // }
        // curl_close($ch);
        $ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$file_contents = curl_exec($ch);
		curl_close($ch);
        return $file_contents;
    }

	public function httpsRequest($url,$data = null,$xparam){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        $Appid = "5a49a451";
        $Appkey = "93d4311daa52485a940b09319ed921ed";
        $curtime = time();
        $CheckSum = md5($Appkey.$curtime.$xparam.$data);
        $headers = array(
        	'X-Appid:'.$Appid,
        	'X-CurTime:'.$curtime,
        	'X-CheckSum:'.$CheckSum,
        	'X-Param:'.$xparam,
        	'Content-Type:'.'application/x-www-form-urlencoded; charset=utf-8'
        	);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    /*语音识别对接科大讯飞*/
    public function getTextVal(){
    	$str = "下雨了没";
    	if (!empty($_GET['name'])) {
    		$str = $_GET['name'];
    	}
    	$name = base64_encode($str);
        $url = "https://api.xfyun.cn/v1/aiui/v1/text_semantic";
        $data = "text=$name";
        $xparam = base64_encode( json_encode(array('scene' => 'main','userid'=>'user_0001' )));
        $res = $this->httpsRequest($url,$data,$xparam);
        dump($res);
    }

    public function getVoice($d){
        $url = "https://api.xfyun.cn/v1/aiui/v1/voice_semantic";
        $xparam = base64_encode( json_encode(array('scene' => 'main','userid'=>'user_0001',"auf"=>"16k","aue"=>"raw","spx_fsize"=>"60" )));
    	file_put_contents('data.txt',$d);
    	$data = "data=".$d;
    	$res = $this->httpsRequest($url,$data,$xparam);
        file_put_contents('res.txt',$res);
        // dump($res);
        // exit();
    	//$res['p'] = $p;
        return $res;
    }

    public function wxupload(){
        $upload_res=$_FILES['viceo'];
        $tempfile = file_get_contents($upload_res['tmp_name']);
        $wavname = substr($upload_res['name'],0,strripos($upload_res['name'],".")).".wav";
        $arr = explode(",", $tempfile);
        $path = 'Application/Admin/Controller/1/'.$upload_res['name'];
        if ($arr && !empty(strstr($tempfile,'base64'))){
        	file_put_contents($path, base64_decode($arr[1]));
        	$res = $this->getVoice($arr[1]);
        }else{
            $path = 'Application/Admin/Controller/2/'.$upload_res['name'];
            $newpath = 'Application/Admin/Controller/2/'.$wavname;
        	file_put_contents($path, $tempfile);
            chmod($path, 0777);
            $exec1 = "avconv -i /home/wwwroot/www.suzwgt.com/$path -vn -f wav /home/wwwroot/www.suzwgt.com/$newpath";
            exec($exec1,$info,$status);
            chmod($newpath, 0777);
            //$d = base64_encode(file_get_contents("./".$newpath));
            $d = file_get_contents("./".$newpath);
	        if ( !empty($tempfile) && $status == 0 ) {
	        	$res = $this->getVoice(base64_encode($d));
	        }else{
                $res = json_encode(array('code'=>'9999','desc'=>'exec执行失败！'));
            }
        }
        echo $res;
        exit();
    }
}