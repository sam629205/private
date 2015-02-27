<?php
error_reporting(0);
$vid = '';
$cid = '';
$s = $_GET["url"];

if($s!=''){
   $arr = explode("://",base64_decode($s));
   $vid = $arr[1];
   $cid = $arr[0];
}

print_r($arr);
echo $cid;
exit;



if($cid=="tudou"){
   $urls = g_url_tudou($vid);
}elseif($cid=="yyt"){
   $urls = g_url_yinyuetai($vid);
}elseif($cid=="pps"){
   $urls = g_url_pps($vid);
}elseif($cid=="youku"){
   $urls = g_url_youku($vid);   
}elseif($cid=="letv"){
   $urls = g_url_letv($vid);
}elseif($cid=="ku6"){
   $urls = g_url_ku6($vid);
}elseif($cid=="sohu"){
   $urls = g_url_sohu($vid);
}elseif($cid=="qq"){
   $urls = g_url_qq($vid);
}elseif($cid=="sina"){
   $urls = g_url_sina($vid);
}elseif($cid=="pptv"){
   $urls = g_url_pptv($vid);
}elseif($cid=="56"){
   $urls = g_url_56($vid);
}elseif($cid=="qiyi"){
   $urls = g_url_qiyi($vid);
}else{
   $urls = "http://movie.ks.js.cn/flv/2011/11/8-1.flv";
}

header("Content-Type:text/html; charset=utf-8");
echo $urls;


function g_url_sina($vid){
         //http://v.iask.com/v_play.php?vid=78917246
         $url = "http://v.iask.com/v_play.php?vid=".$vid;
		 $video_data = g_url_contents($url);
		 
		 $ua =explode("<url><![CDATA[",$video_data);
		 $surl="";
		 for($i=1;$i<count($ua);$i++){
		     $ia = explode("]]></url>",$ua[$i]);
			 
			 if($i>1){
			    $surl.="|".$ia[0];
			 }else{
			    $surl.=$ia[0];
			 }
		 }
		 
		 return $surl;	 
}


function g_url_qq($vid){
         //f00118wmfib
         $url = "http://vv.video.qq.com/geturl?vid=".$vid;
		 $video_data = g_url_contents($url);
		 preg_match('|<url>(.*?)</url>|',$video_data,$ua);
		 return $ua[1];	 
}

function g_url_56($vid){
    $content=g_url_contents('http://vxml.56.com/json/'.substr($vid,0,11).'/?src=out');
	$data=json_decode($content);
	$wd2=$data->info->rfiles;
	for($i==0;$i<count($wd2);$i++){
		$type=$wd2[$i]->type;
		if($type=='wvga'){
			$wd3=$wd2[$i]->url;
			break;
		}
	}
	if(!$wd3){
		$wd3=$wd2[0]->url;
	}
	return $wd3;	 
}

function g_url_pptv($vid){
         //http://client-play.pplive.cn/chplay3-0-15879657.xml
		 
		 $url = "http://client-play.pplive.cn/chplay3-0-".$vid.".xml";
		 $video_data = g_url_contents($url);

		 $xml = simplexml_load_string($video_data);
		 $rid = $xml->channel[rid];
         $st = $xml->dt->st;
         $st = str_replace('Wed', 'Sat', $st);
         $md5 = md5($st);
		 $bh = $xml->dt->bh;
         $drag = $xml->drag->sgm;
         $num = count($drag);
         
		 for ($i = 0; $i < $num; $i++) {
                $d = $i +1;
				if($i==0){
                   $iurl = "http://$bh/$i/$rid?key=$md5";
				}else{
				   $iurl .= "|http://$bh/$i/$rid?key=$md5";
				}   
        }
		 
		 
		 
		 return $iurl;	 
}

function g_url_sohu($vid){
         $url = "http://hot.vrs.sohu.com/vrs_flash.action?vid=".$vid;
		 $video_data = g_url_contents($url);

		 $data = json_decode($video_data);
		 $va = $data->data->su;
		 $i = 0;
		 $surl = "";
		 $t = "http://220.181.61.229/?prot=2&file=&new=";
		 foreach($va as $k){
			  $urls = $t.$k;
              $vs = g_url_contents($urls);
			  $ia = explode("|",$vs);
			  
			  if($i>0){
			    $surl .= "|".$ia[0].$k."?key=".$ia[3];
			  }else{
			    $surl .= $ia[0].$k."?key=".$ia[3];
			  }
              $i++;			
		 }
		 return $surl;
		 
}



function g_url_ku6($vid){
         //wS90iS7yJsD99DK1KTbPTA..
         $url = "http://v.ku6.com/fetch.htm?t=getVideo4Player&vid=".$vid;
		 $video_data = g_url_contents($url);
		 $data = json_decode($video_data);
		 $va = $data->data->f;
		 $va = explode(",",$va);
		 $urls = "";
		 $i = 0;
		 foreach($va as $k){
		    if($i==0){
			  $urls .= $k; 
			}else{
			  $urls .= '|'.$k;
			}
            $i++;			
		 }
		 return $urls;
		 
}


function g_url_letv($vid){
         $url = "http://app.letv.com/v.php?id=".$vid;
		 $video_data = g_url_contents($url);
		 
		 preg_match('/"url":"(.*?)\"\}\]/', $video_data, $match_url);
		 
		 $vurl = str_replace("\/","/",$match_url[1]);
		 $vurl = str_replace("ng?s=3&df=","",$vurl);
		 
		 if($vurl==''){
		    $vurl = g_url_letv_xml($vid);
		 }
		 
		 if($vurl==''){
		    $vurl = g_url_letvs($vid);
		 }
		 
		 return $vurl;
}

function g_url_letv_xml($vid){
         $url = "http://www.letv.com/v_xml/".$vid.".xml";
		 $video_data = g_url_contents($url);
		 preg_match('/\[low=(.*?)\]\]/', $video_data, $match_url);
		 $vurl = str_replace("\/","/",$match_url[1]);
		 $vurl = urldecode($vurl);
		 $vurl = str_replace("ng?s=3&df=","",$vurl);
		 $ua = explode("&br=",$vurl);
		 return $ua[0]; 
}

function g_url_letvs($vid){
		 $url = "http://www.flvcd.com/parse.php?kw=http://www.letv.com/ptv/vplay/".$vid.".html";
		 $video_data = g_url_contents($url);
		 preg_match('|clipurl = "(.*?)";var|',$video_data,$ua);
		 return $ua[1];	
}


function g_url_youku($vid){
         $url = "http://v.youku.com/player/getPlayList/VideoIDS/".$vid;
		 $video_data = g_url_contents($url);
		 
		 $data = json_decode($video_data);
		 
		 $fileid_=$data->data[0]->streamfileids;
	     $fileid2_=$fileid_->hd2;
	     $sk='hd2';
	     if(!$fileid2_){
		    $fileid2_=$fileid_->mp4;
		    $sk='mp4';
	     }
	     if(!$fileid2_){
		    $fileid2_=$fileid_->flv;
		    $sk='flv';
	    }
	    $sid=getSid();
	    $fileid3_=getfileid($fileid2_,$data->data[0]->seed);
	    $filed1_=substr($fileid3_,0,8);
	    $filed2_=substr($fileid3_,10);
	    $segs=$data->data[0]->segs->$sk;
	    $i=0;
	    foreach($segs AS $seg1 => $v1){
		$AA= strtoupper(dechex($i)).'';
		if(strlen($AA)<2) $AA='0'.$AA;
		$i+=1;
		$filed_=$filed1_.$AA.$filed2_;
		$k1=$v1->k;
		$k2=$v1->k2;
		if($i>1){
			$urllist.='|';	
		}
		$sk=str_replace('hd2','flv',$sk);
		$urllist.='http://f.youku.com/player/getFlvPath/sid/00_00/st/'.$sk.'/fileid/'.$filed_.'?K='.$k1.',k2='.$k2;
	    }
	return $urllist;
}


function g_url_pps($vid){
         $url = "http://dp.ppstream.com/get_play_url_cdn.php?sid=$vid&flash_type=1&type=0";
		 $video_data = g_url_contents($url);
		 $ua = explode("?",$video_data);
		 return $ua[0];
}


function g_url_yinyuetai($vid){
    $url = "http://www.yinyuetai.com/mvplayer/get-video-info?flex=true&videoId=".$vid;
    $video_data = g_url_contents($url);
    $uArr = explode("http://",$video_data);
    for($i=0;$i<count($uArr);$i++){
         $iArr = explode(".flv",$uArr[$i]);
         if(count($iArr)>1){
           return 'http://'.$iArr[0].'.flv';
         }
    }
    return '';
}


function g_url_tudou($vid){
         $url = "http://v2.tudou.com/v2/cdn?id=".$vid;
         $video_data = g_url_contents($url);
         preg_match('/<f w=\"([0-9]+)\"/', $video_data, $match_num);
         preg_match('/http:\/\/(.*?)\?/', $video_data, $match_url);
         preg_match('/key=([\w]+)/', $video_data, $match_key);
         $flv_link = 'http://'.$match_url[1].'?'.$match_num[1].'&key='.$match_key[1].'&id=tudou';
         return $flv_link;
}



function g_url_qiyi($vid){
         // http://cache.video.qiyi.com/v/e0a98327e49a465fad3ec3f2293ce2d0
		 $url = "http://cache.video.qiyi.com/v/$vid";
		 $video_data = g_url_contents($url);
		 preg_match('|<videoUrl>(.*?)</videoUrl>|',$video_data,$ua);
		 $url = 'http://www.flvurl.cn/?url='.$ua[1];
		 $video_data = g_url_contents($url);
		 $ua = explode("网址如下:",$video_data);
		 
		 $surl = '';
		 $ua = explode("listen('",$ua[1]);
		 for($i=1;$i<count($ua);$i++){
		     $ia = explode("');",$ua[$i]);
			 if($i>1){
			    $surl.="|".$ia[0];
			 }else{
			    $surl.=$ia[0];
			 }
		 }
		 
		 return $surl;
}








//函数库

function g_url_contents($url){
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $ch = curl_init();
        $timeout = 30;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        curl_setopt($ch,CURLOPT_USERAGENT,$user_agent);
        @ $c = curl_exec($ch);
        curl_close($ch);
        return $c;
}


function getSid() {
    $sid = time().(rand(0,9000)+10000);
    return $sid;
}
function getkey($key1,$key2){
    $a = hexdec($key1);
    $b = $a ^ 0xA55AA5A5;
    $b = dechex($b);
    return $key2.$b;
}
function getfileid($fileId,$seed) {
    $mixed = getMixString($seed);
    $ids = explode("*",$fileId);
    unset($ids[count($ids)-1]);
    $realId = "";
    for ($i=0;$i < count($ids);++$i) {
    $idx = $ids[$i];
    $realId .= substr($mixed,$idx,1);
    }
    return $realId;
}
function getMixString($seed) {
    $mixed = "";
    $source = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ/\\:._-1234567890";
    $len = strlen($source);
    for($i=0;$i< $len;++$i){
    $seed = ($seed * 211 + 30031) % 65536;
    $index = ($seed / 65536 * strlen($source));
    $c = substr($source,$index,1);
    $mixed .= $c;
    $source = str_replace($c, "",$source);
    }
    return $mixed;
}


function bin2bstr($input){
  if (!is_string($input)) return null; // Sanity check
  $input = str_split($input, 4);
  $str = '';
  foreach ($input as $v){
   $str .= base_convert($v, 2, 16);
  }
  $str =  pack('H*', $str);
  return $str;
}

function bstr2bin($input){
  if (!is_string($input)) return null;
  $value = unpack('H*', $input);
  $value = str_split($value[1], 1);
  $bin = '';
  foreach ($value as $v){
   $b = str_pad(base_convert($v, 16, 2), 4, '0', STR_PAD_LEFT);
   $bin .= $b;
  }
  return $bin;
}
?>
