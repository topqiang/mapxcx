<?php
namespace Api\Controller;
use Think\Controller;
class UserController extends Controller {
	//用户登录

	//周边wifi

	public function getWifi(){
		header('Content-type:text/html;charset=utf-8');
		//配置您申请的appkey
		$appkey = "4ad5dfa6f8a2d0aef3007582abfde133";
		//************1.查询周边WIFI************
		$data['uid'] = $_POST['uid'];
        $data['keyword'] = $this -> filter_mark($_POST['keyword']);
        $has = $this -> history -> where( $data ) -> limit(1) -> select();
		$data['lat'] = $_POST['latitude'];
		$data['lnt'] = $_POST['longitude'];
        $distinct = $_POST['distinct'];
        $data['ctime'] = time();
		$data['status'] = 0;
		$url = "http://apis.juhe.cn/wifi/local";
		$params = array(
		      "lon" => $data['lnt'],//经纬(如:121.538123)
		      "lat" => $data['lat'],//纬度(如：31.677132)
		      "gtype" => "3",//所传递经纬类型 1：百度  2：谷歌 3：gps
		      "r" => $distinct,//搜索范围，单位M，默认3000
		      "key" => $appkey//应用APPKEY(应用详细页查询)
		);
		$paramstring = http_build_query($params);
		$content = juhecurl($url,$paramstring);
		$result = json_decode($content,true);
		if($result){
		    if($result['error_code']=='0'){
		        if (!empty($has)) {
	                $res = $this -> history -> where('id='.$has[0]['id'])->setInc('num');
	            }else{
	            	$res = $this -> history -> add( $data );
	            }
		        print_r($result);
		    }else{
		        echo $result['error_code'].":".$result['reason'];
		    }
		}else{
		    echo "请求失败";
		} 
		
	}
	 
 
 
	/**
	 * 请求接口返回内容
	 * @param  string $url [请求的URL地址]
	 * @param  string $params [请求的参数]
	 * @param  int $ipost [是否采用POST形式]
	 * @return  string
	 */
	function juhecurl($url,$params=false,$ispost=0){
	    $httpInfo = array();
	    $ch = curl_init();
	 
	    curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
	    curl_setopt( $ch, CURLOPT_USERAGENT , 'JuheData' );
	    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 60 );
	    curl_setopt( $ch, CURLOPT_TIMEOUT , 60);
	    curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    if( $ispost )
	    {
	        curl_setopt( $ch , CURLOPT_POST , true );
	        curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
	        curl_setopt( $ch , CURLOPT_URL , $url );
	    }
	    else
	    {
	        if($params){
	            curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
	        }else{
	            curl_setopt( $ch , CURLOPT_URL , $url);
	        }
	    }
	    $response = curl_exec( $ch );
	    if ($response === FALSE) {
	        //echo "cURL Error: " . curl_error($ch);
	        return false;
	    }
	    $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
	    $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
	    curl_close( $ch );
	    return $response;
	}
}