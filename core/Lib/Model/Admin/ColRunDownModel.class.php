<?php 
/**
 * @name    自定义采集执行模块
 * @package GXCMS.Administrator
 * @link    www.gxcms.com
 */
class ColRunDownModel extends AdvModel {
    private $DB;
    private $ContDB;
    private $UrlDB;
    private $CModel;
	private $VideoDB;
    function __construct(){
		$this->DB     =D('co_node');
		$this->ContDB =D('co_content');
		$this->UrlDB  =D('co_urls');
		$this->VideoDB=D('video');
		$this->CModel =D('Admin.CustomCollectDown');
		//Load('extend');
    }
    
    function GetParam(){
    	$Get=getReq($_REQUEST,array(
    	                'action'=>'string',
						'type'  =>'string',//采集类型 url/content
						'nid'   =>'int',   //节点id
    	                'page'  =>'int' ,
    	                'stime' =>'int',
						'day'   =>'int',//当天/本周/所有
						'qid'   =>'int',//起始ID
						'jid'   =>'int',//结束ID
						'scid'  =>'int',//栏目ID
						'snull' =>'int',//资源是否为空
					));
		if($Get['type']=='content'){
			$Get=array_merge($Get,getReq($_REQUEST,array( 'total' =>'int','pagesize'  =>'int','cmode'=>'int','action'=>'string')));//采集内容 每页采集数、间隔时间 
			if(empty($Get['pagesize']))  $Get['pagesize']=1;
			//if(empty($Get['stime']))     $Get['stime']=3;
		}
		//if($Get['action']=='real'){$Get['stime']=$_GET['stime'];}
         return $Get;
    }
    
    function Check($Get){
       if(empty($Get['nid'])) {$this->error='参数错误'; return false;}
       $where['id']=intval($_GET['nid']);
       $Data=$this->DB->where($where)->find();
       if (!$Data) {$this->error='该节点不存在或已删除'; return false;}
       
       if(!empty($Get['action'])){ 
      	$Data['action']=$Get['action'];
       }else {
      	$this->error='参数错误'; 
      	return false;
       }
       
       if( isset($Get['total']))    $Data['total'] = intval($Get['total']);
       if( isset($Get['page']))     $Data['page'] = intval($Get['page']);
       if( isset($Get['stime']))    $Data['stime']= intval($Get['stime']);
       if( isset($Get['day']))      $Data['day']= intval($Get['day']);
	   if( isset($Get['qid']))      $Data['qid']= intval($Get['qid']);
	   if( isset($Get['jid']))      $Data['jid']= intval($Get['jid']);
	   if( isset($Get['snull']))    $Data['snull']= intval($Get['snull']);
	   if( isset($Get['scid']))     $Data['scid']= intval($Get['scid']);
	   if( isset($Get['pagesize'])) $Data['pagesize']= intval($Get['pagesize']);
       if( isset($Get['cmode']))    $Data['cmode']= intval($Get['cmode']);//采集模式
       return $Data;
    }
    
    
    /**
     * 采集资源Search
     */
    function ColVideoSearch(){
    	if(!empty($_REQUEST['nid']))   $Get['nid']  =trim($_REQUEST['nid']);
    	if(!empty($_REQUEST['title'])) $Get['title']=trim($_REQUEST['title']);
    	 return  $Get;           
    }
    
    /**
     * 采集资源入库list
     * @param $where
     */
    function ColVideoList($where){
    	if(!empty($where['title'])){
    		$title=$where['title'];
    		$where['title']=array('like','%'.$title.'%'); 
    	}
    	$order=empty($_GET['order'])?'addtime':$_GET['order'];
    	$sort =empty($_GET['sort'])?'desc':$_GET['sort'];
    	$order=$order.' '.$sort;
    	$where['status']=array('neq',0);
		$where[C('db_prefix').'co_content.class']= array('eq',2); //资源采集分类
    	$video_count = $this->ContDB->where($where)->count('id');
		$video_page  = !empty($_GET['p']) ? intval($_GET['p']) : 1;
		$video_page  = get_cms_page_max($video_count,C('web_admin_pagenum'),$video_page);
		$video_url   = U('Admin-CustomCollectDown/ColDown',array('nid'=>urlencode($where['nid']),'title'=>urlencode($title),'p'=>''),false,false);
		$pagelist   = get_cms_page($video_count,C('web_admin_pagenum'),$video_page,$video_url,'条记录');
		$_SESSION['video_reurl'] = $video_url.$video_page;	
	$Arr=$this->ContDB->field(C('db_prefix').'co_content.id,did,nid,name,status,url,data,addtime')->join(C('db_prefix').'co_node on '.C('db_prefix').'co_node.id=nid')
	      ->where($where)->order(C('db_prefix').'co_content.'.$order)->limit(C('web_admin_pagenum'))->page($video_page)->select();
	    //echo $this->ContDB->getLastSql();
	foreach($Arr as $key=>$val){
		if(!empty($val['data'])){ 
		 $val['data']=stripslashes($val['data']);	
		 $Arr[$key]['data']=collect::string2array($val['data']);
		 $Arr[$key]['base']=$this->ConvShow($Arr[$key]['data']);
		}
	}
	
	
	
	$ArrReturn=array('video'=>$Arr,'pagelist'=>$pagelist,'order'=>$order,'title'=>$title,'nid'=>$where['nid']);
	return 	$ArrReturn;
    	
    }

	 /**
     * 节点资源list
     * @param $where
     */
    function ColNidList($where,$Data){
    	if(!empty($where['title'])){
    		$title = urlencode(trim($where['title']));
    	}
    	if($Data['keymode']==1){
			$title = urlencode(trim(iconv("UTF-8", "GBK",$where['title'])));
		}
		$ListUrls = array(str_replace('(*)', $title, $Data['urlpage']),$title);
		$ArtUrl = $this->GetArtlist($ListUrls[0], $Data, true);//资源url
		return $ArtUrl;
    }
    
    /**
     * 采集网址list
     * @param array $Data  DB&Get
     * 
     */
    function CoUrl(&$Data){ 
       $ListUrls = $this->GetList($Data);//获取列表页urls
	   $TotalPage = count($ListUrls); 
			if ($TotalPage > 0) {
				$Page    = !empty($Data['page']) ? intval($Data['page']) : 0;
				$CurList = $ListUrls[$Page][0];//当前采集列表页url
				$CurID = $ListUrls[$Page][1];
				$ArtUrl = $this->GetArtlist($CurList, $Data);//资源url
				$ArtTotal= count($ArtUrl);
				$reNum =0;//重复记录个数
				$newNum=0;
			if (is_array($ArtUrl)) {
				foreach ($ArtUrl as $v) {//处理预定义及html标签
					//if (empty($v['url']) || empty($v['title'])) continue;
					if (empty($v['url'])) continue;
					$v = collect::Doaddslashes($v);
					$v['title'] = strip_tags($v['title']);
					$did = $CurID;
					if($Data['action']==='real'){//入库
						$where['md5'] = md5($v['url']);
						if (!$this->UrlDB->where($where)->find()) {//若不存在,更新入库
							$where['nid']=$Data['id'];
							$this->UrlDB->data($where)->add();
							$this->ContDB->data(array('nid'=>$where['nid'], 'status'=>0, 'class'=>2, 'url'=>$v['url'], 'did'=>$did, 'title'=>$v['title'],'addtime'=>time()))->add();					
						    $newNum++;
						} else {//重复url 若ContDB中记录存在则更新status为0
							$cwhere['nid']=$Data['id'];
							$cwhere['url']=$v['url'];
							if($this->ContDB->where($cwhere)->find()){
								$this->ContDB->where($cwhere)->setField('status',0);
							}
							$reNum++;
						}
				   }
				  
				}
			    if($Data['action']==='real'){//入库
					if ($TotalPage <= $Page) {
						$this->DB->where('id='.$Data['id'])->save(array('lastdate'=>time()));
						//$this->error='采集完成！';		
					}
			    }
				$UrlArr=array(
				       'nid'     =>$Data['id'],
				       'name'    =>$Data['name'],
				       'url_list'=>$CurList,
				       'url'     =>$ArtUrl,
				       'page'    =>$Page+1,
				       'total_page'=>$TotalPage,
				       'total'   =>$ArtTotal,
				       'reNum'   =>$reNum,
				       'newNum'  =>$newNum,
				       'newAdd'  =>$ArtTotal-$reNum,
				       'action'  =>$Data['action'],
				       'stime'   =>$Data['stime'],
				       'day'     =>$Data['day'],
				       'qid'     =>$Data['qid'],
				       'jid'     =>$Data['jid'],
				       'scid'     =>$Data['scid'],
				       'snull'     =>$Data['snull'],
				      );
				return $UrlArr;
			}else{ 
				   $this->error='采集失败，获取资源列表失败，[可能目标站屏蔽了词汇，等待1分钟解除屏蔽后采集下一条]';
			       //continue;
				   sleep('1');
				$UrlArr=array(
				       'nid'     =>$Data['id'],
				       'name'    =>$Data['name'],
				       'url_list'=>$CurList,
				       'url'     =>$ArtUrl,
				       'page'    =>$Page+1,
				       'total_page'=>$TotalPage,
				       'total'   =>$ArtTotal,
				       'reNum'   =>$reNum,
				       'newNum'  =>$newNum,
				       'newAdd'  =>$ArtTotal-$reNum,
				       'action'  =>$Data['action'],
				       'stime'   =>$Data['stime'],
				       'day'     =>$Data['day'],
				       'qid'     =>$Data['qid'],
				       'jid'     =>$Data['jid'],
				       'scid'     =>$Data['scid'],
				       'snull'     =>$Data['snull'],
				      );
				return $UrlArr;
				   //return false;
			   }
			} else {
				   $this->error='采集失败，没有可采集的网址';
				   return false;
			}
		}


    /**
     * collect demo
     * @param array $Url   第一个采集url array(title/url) 
     * @param array $Data  节点信息
     * @return array       采集结果
     */
     function Contest($Url,&$Data){//demo
     	$html=$this->GetCon($Url, $Data);
     	$Arr = $this->ConvShow($html);
		return $Arr;
     }
     
     /**
      * 转换显示采集内容结果
      * @param array $html
      */
     function ConvShow(&$html){
     	$BaseArr=$this->CModel->BaseArr();
		foreach($BaseArr as $k=>$v){  //选择不采集的字段将不在采集结果中显示
		   if(array_key_exists($k,$html)) $arr[$v]=$html[$k];
		}
     	return $Return=array('base'=>$arr,'downurl'=>$html['downurl']);
     }
     
     function GentHtml($id){
     	$html=$this->ContDB->where('id='.$id)->select();
     }

     
     function Collecting(&$config){
     	    $page  = !empty($config['page'])  ? intval($config['page']) : 1;
			$all   = !empty($config['total']) ? intval($config['total']) : 0;
			$where=array('nid'=>$config['id'], 'status'=>'0');
			//$where=array('nid'=>$config['id']);
			$total=$this->ContDB->where($where)->count();
			if(empty($all)) $all=$total;
			$total_page = ceil($all/$config['pagesize']);
			$maxpage=get_cms_page_max($total,$config['pagesize'],$page);
			$list=$this->ContDB->where($where)->order('id desc')->limit($config['pagesize'])->page($maxpage)->select();
             if($page<=1) $this->DB->where('id='.$config['id'])->save(array('lastdate'=>time()));
			//$i = 0;
			//$list = array($list[0]);
			if (!empty($list) && is_array($list)) {
			   if ($total_page <$page) {
					//$this->DB->where('id='.$config['id'])->save(array('lastdate'=>time()));
					F('_gxcms/ColCache',NUll);
					$this->error='采集完成！';
					return true;
				}
				foreach ($list as $v) {			
					$html  =$this->GetCon($v, $config);
					$html['did'] = $v['did'];
					$msg='<strong>'.$html['title'].'<font color=yellor>--></font>'.$html['did'].'</strong>';
					$stauts=1;
					if($config['direct']){//直接入库video
						if(!$this->CModel->videoImport($v['url'],$html)){
						$msg.=$this->CModel->getError();
					    //continue;
						}else{
						$msg.='资源入库保存成功.';
						$stauts=2;
						}
					}
					$updata=array('status'=>$stauts,'data'=>collect::array2string($html),'addtime'=>time());
					if(!empty($html['title']))  $updata['title']=$html['title'];
					$Update=$this->ContDB->where('id='.$v['id'])->save($updata);
					if($Update==='false'){//失败则跳过		
					$msg.=' 采集入库失败！';
					continue;
					}else{$msg.=' 采集成功.';}
					 	
					//$i++;
					if($config['stime']>0)  {//间隔时间
					sleep($config['stime']);
					$msg.='暂停'.$config['stime'].'秒继续采集...'; 
					}
					$result=array('con'=>$this->ConvShow($html),'url'=>$v['url'],'msg'=>$msg);
				  
				}
	            $StrUrl='?s=Admin/CustomCollectDown/ColRun/action/real/type/content/nid/'.$config['id'].'/page/'.($page+1).'/total/'.$all.'/day/'.$config['day'].'/qid/'.$config['qid'].'/jid/'.$config['jid'].'/scid/'.$config['scid'].'/stime/'.$config['stime'].'/snull/'.$config['snull'];
	            $result['StrUrl'] =$StrUrl;
	            $result['total']  =$all;
	            $result['already']=$page*$config['pagesize'];
	            $result['percent']=($result['already']/$result['total'])*200;
	            
			    if ($total_page >=$page){//添加缓存--续采
			    	$CacheUrl='?s=Admin/CustomCollectDown/ColRun/action/real/type/content/nid/'.$config['id'].'/page/'.$page.'/total/'.$all.'/day/'.$config['day'].'/qid/'.$config['qid'].'/jid/'.$config['jid'].'/scid/'.$config['scid'].'/stime/'.$config['stime'].'/snull/'.$config['snull'];
			    	F('_gxcms/ColCache',$CacheUrl);
			    } 
	            return $result;							
			}else {
				   if(F('_gxcms/ColCache')) F('_gxcms/ColCache',NUll);
					$this->error='没有要采集的资源';
					return false;
			}
            
     	
     }
     
     
		
    /**
	 * 获取资源网址
	 * @param string $url           采集地址
	 * @param array $config         配置
	 */
     function GetArtlist($url, &$config, $list) {
		if ($html = collect::get_html($url, $config)) {
			if ($config['sourcetype'] == 4) { //RSS
				$html = xml::xml_unserialize($html);
				$data = array();
				if (is_array($html['rss']['channel']['item']))foreach ($html['rss']['channel']['item'] as $k=>$v) {
					$data[$k]['url'] = $v['link'];
					$data[$k]['title'] = $v['title'];
				}
			}else if ($config['sourcetype'] == 3) { //直接从内容页采集
				$data = array();
				$data[]=array('url'=>$url);
			} else {
				$html = collect::cut_html($html, $config['url_start'], $config['url_end']);
				$html = str_replace(array("\r", "\n"), '', $html);
				$html = str_replace(array("</a>", "</A>"), "</a>\n", $html);
				preg_match_all('/<a([^>]*)>([^\/a>].*)<\/a>/i', $html, $out);
			    $data = array();
			    	
				//移除重复数据
				$out[1] = array_unique($out[1]);//url
				$out[2] = array_unique($out[2]);//title
				foreach ($out[1] as $k=>$v) {
					if (preg_match('/href=[\'"]?([^\'" ]*)[\'"]?/i', $v, $match_out)) {
						if ($config['url_contain']) {
							if (strpos($match_out[1], $config['url_contain']) === false) {
								continue;
							} 
						}
						if ($config['url_except']) {
							if (strpos($match_out[1], $config['url_except']) !== false) {
								continue;
							} 
						}
						$url2 = $match_out[1];
						$url2 = collect::url_check($url2, $url, $config);
						$data[$k]['url'] = $url2;
						$data[$k]['title'] = strip_tags($out[2][$k]);//去除标签
					}
				}
				if($config['colmode']=='desc')$data=get_collect_krsort($data);
				
			}
			if($list == true){
				return $data;
			}else{
				return array($data[0]);
			}
		} else {
			return false;
		}
	}
	



    /**
	 * 采集内容
	 * @param arr $source    采集地址 [url/title/picurl]
	 * @param array $config  配置参数
	 * @param integer $page  分页采集模式  暂无作用
	 */
	function GetCon($source, &$config, $page = 0) {
		set_time_limit(300);
		static $oldurl = array();
		$page = intval($page) ? intval($page) : 0;
		$url=$source['url'];
		if ($html = collect::get_html($url, $config)) {
			if (empty($page)) {
			    if(isset($config['fields'])) $config['fields']=collect::string2array($config['fields']);//字符串转换为数组
			   
				if(is_array($config['fields'])){
					foreach($config['fields'] as $k=>$v){
						//匹配规则,获取内容						
						if ($config[$v.'_rule']) {							
							$ArrRule = collect::replace_sg($config[$v.'_rule']);
							$data[$v]= collect::replace_item(collect::cut_html($html, $ArrRule[0], $ArrRule[1]), $config[$v.'_filter']);
						}
					}
				}
				/*=================================================*/
				//下载地址
				if($config['range']=='1'){//下载列表范围
					 $plist=collect::cut_html($html, $config['downlist_start'], $config['downlist_end']);
					 $plist=str_replace('<tr><td width=\'85%\' class=\'td_thunder\'><input type=\'checkbox\' name=\'enjoy1[]\' id=\'enjoy1[]\' value=\'\'/><a href=\'javascript:void(0)\' onclick=\'return OnDownloadClick_Simple(this,1)\' oncontextmenu=\'return ThunderNetwork_SetHref(this)\'>========================下载地址2=============================</a></td><td style=\'display:none\'></td></tr>','',$html);
				}else{
					$plist=&$html;
					$plist=str_replace('<tr><td width=\'85%\' class=\'td_thunder\'><input type=\'checkbox\' name=\'enjoy1[]\' id=\'enjoy1[]\' value=\'\'/><a href=\'javascript:void(0)\' onclick=\'return OnDownloadClick_Simple(this,1)\' oncontextmenu=\'return ThunderNetwork_SetHref(this)\'>========================下载地址2=============================</a></td><td style=\'display:none\'></td></tr>','',&$html);
				}
				
				if($config['downmode']=='1') {//下载页获取单集下载url
					if($config['downlink_rule']) {
					 $out=$this->MatchAll($plist,$config['downlink_rule']);
				     foreach($out as $key=>$val){
				     $downlink[$key]= collect::replace_item($val, $config['downlink_filter']);
				     $downlink[$key]= collect::url_check($downlink[$key], $url, $config);
				     $phtml[$key] = collect::get_html($downlink[$key], $config);
				     if($config['durl_range']=='1'){
				     $phtml[$key]=collect::cut_html($phtml[$key], $config['downurl_start'], $config['downurl_end']);
				     }
				      //获取下载url 	
				     if ($config['downurl_rule']) {
				     	$ArrRule = collect::replace_sg($config['downurl_rule']);
					    $data['downurl'][$key]= collect::replace_item(collect::cut_html($phtml[$key], $ArrRule[0], $ArrRule[1]), $config['downurl_filter']);
				     }
				     }		  
					}
			  			
				}else{
						if( $config['downmode']=='2'){//下载页获取所有下载url
						     if ($config['downlink_rule']) {
							 $ArrRule = collect::replace_sg($config['downlink_rule']);
							 $downlink= collect::replace_item(collect::cut_html($plist, $ArrRule[0], $ArrRule[1]), $config['downlink_filter']);
							 }
							 $downlink = collect::url_check($downlink, $url, $config);
							 $phtml = collect::get_html($downlink, $config);
							 if($config['durl_range']=='1'){
				     	     $phtml=collect::cut_html($phtml, $config['downurl_start'], $config['downurl_end']);
				             }
						}else{
						       $phtml=&$plist;//默认内容页获取所有下载url
						}
						
						if ($config['downurl_rule']) {//downurl 单集下载地址
					    	 $out=$this->MatchAll($phtml,$config['downurl_rule']);
						     foreach($out as $key=>$val){
						     $data['downurl'][]= collect::replace_item($val, $config['downurl_filter']);
						     }
						}
					
					
				}
                /*================================*/
				//获取分集名称
				if($config['vnamemode']==2){
				    if ($config['vname_rule']) {	
				    	 $vout=$this->MatchAll($phtml,$config['vname_rule']);
				         foreach($vout as $key=>$val){
						 $data['vname'][]= collect::replace_item($val, $config['vname_filter']);
						 }	
						 foreach($data['downurl'] as $k=>$v){
						 //$pattern = '/^[\[|【](.*?)[\]|】]/';
						 //preg_match($pattern,$data['vname'][$k],$matches);
						 if (stristr($v,"xzurl=") == false && stristr($v,"cid=") == false && stristr($v,"http") == false && stristr($v,"ftp") == true){
							$v = 'xzurl='.$v;
						 }
						 if (stristr($v,"http") == false && stristr($v,"ftp") == false && stristr($v,"cid=") == false){
							$v = 'cid='.$v;
						 }
						 //$data['vname'][$k] = preg_replace('/^[\[|【](.*?)[\]|】]/','',$data['vname'][$k]);
						 $data['vname'][$k] = str_replace('&nbsp;','',$data['vname'][$k]);
						 $data['downurl'][$k]=$data['vname'][$k].'$'.$v.'&mc='.C('down_bf').$data['vname'][$k];
						 }			
					}
				}
		
				/*=================================================*/
			}
			/*if ($page == 0) {
				$data['content'] = preg_replace('/<img[^>]*src=[\'"]?([^>\'"\s]*)[\'"]?[^>]*>/ie', "collect::download_img('$0', '$1')", $data['content']);
			}*/
			return $data;
		}
	}
	
	
	    /**
		 * 得到需要采集的网页列表页
		 * @param array $config 配置参数
		 * @param integer $num  返回数
		 */
		 function GetList(&$config, $num = '') {
			$url = array();
			$where['dlock'] = array('eq',1);
			$where['status'] = array('eq',1);
			if(trim($config['snull'])==1){
				$where['downurl'] = array('eq','');
			}
			if(trim($config['day'])!=0){
				$where['addtime'] = array('gt',xtime(trim($config['day'])));
			}
			if(trim($config['qid'])!='' && trim($config['qid'])!=0){
				if(trim($config['jid'])!='' && trim($config['jid'])!=0){
					$where['id'] = array('between',trim($config['qid']).','.trim($config['jid']));
				}else{
					$where['id'] = array('gt',$config['qid']);
				}
			}
			if(trim($config['scid'])!='' && trim($config['scid'])!=0){
				$where['cid'] = array('eq',trim($config['scid']));
			}
			$video_count = $this->VideoDB->where($where)->count('id');
			$list = $this->VideoDB->field('id,title')->where($where)->order('addtime desc')->select();
			foreach($list as $key=>$value){
				//$title = str_replace('/',' ',$value['title']);
				//$title = htmlspecialchars(urldecode(trim($value['title'])));
				if($config['keymode']==0){
					$title = urlencode(trim($value['title']));
				}else{
					$title = urlencode(trim(iconv("UTF-8", "GBK",$value['title'])));
				}
				$url_s = array(str_replace('(*)', $title, $config['urlpage']),$value['id']);
				$url[$key] = $url_s;
			}
			return $url;
		}
		
		/**
		 * 匹配获取所有
		 * @param string config_rule
		 * @return Array
		 */
		function MatchAll(&$html,$rule){
			$ArrRule = collect::replace_sg($rule);
			foreach($ArrRule as $key=>$val){
			$ArrRule[$key]=collect::str_replace_all($val);
			}
			$str = "/".$ArrRule[0]."([\s\S]*?)".$ArrRule[1]."/";
			preg_match_all($str, $html, $out);
			if($out) return $out[1];
			return false;
		}
		
		
		function Getdownurl(&$html,$rule,$filter){
			$out=$this->MatchAll($html,$rule);
			foreach($out as $key=>$val){
			$data['downurl'][]= collect::replace_item($val, $filter);
			}
			return $data['downurl'];   
		}
		

		
		/**
		 * 采集资源入库管理
		 * @param $act
		 */
		function Inflow($act){
			if($act=='inflow'){
				if(empty($_POST['ids'])) {
					$this->error='请选择需入库资源!';
					return false;
				}
				$ArrID=$_POST['ids'];
			}else {
				$where['status']=array('neq',0);
				$where['class']=array('eq',2);
				if($act=='today')     $where['addtime']=array('gt',xtime(1));
				if($act=='allunused') $where['status']=0;
				if($act=='allinflow') $where='';
				
				$All=$this->ContDB->field('id')->where($where)->select();
				foreach($All as $k=>$v){
				$ArrID[$k]=$v['id'];
				}
				
			}
			foreach($ArrID as $key=>$val){
				$Cont=$this->ContDB->field('nid,url,data,did')->where('id='.$val)->find();
				$data=collect::string2array(stripslashes($Cont['data']));
				$data['did']=$Cont['did'];
				$result.='<strong>'.$data['title'].'</strong>';
				if(!$this->CModel->videoImport($Cont['url'],$data,$Cont['nid'])){
			    $result.=$this->CModel->getError()."\n\r";
			     continue;
				}else{
				$UpCont=array('status'=>2);
				/*if($data['cid']==999){
					unset($data['cid']);
					$UpCont['data']=collect::array2string($data);
				}*/	
				$Update=$this->ContDB->where('id='.$val)->save($UpCont);
				$result.="入库成功!\n\r";	
				}	
			}
			$result=explode("\n\r",$result);
			return $result;
			//if(!empty($this->error)) return false;
			//return true;
		}
			
		 /**
		 * 采集资源删除
		 * @param $act
		 * 是否删除源链接库数据？
		 */	
	    function Del($act){
	  	 if($act=='delselect' || $act=='delonly'){//delonly 不希望再次采集，不删除url
	       if(!empty($_POST['ids'])) {
	       	$where['id']=array('in',$_POST['ids']);
	       /****************************/
	       	//删除历史url表记录
	       	if($act=='delselect'){
	       	  $urls=$this->ContDB->field('url')->where($where)->select();
	       	  foreach($urls as $k=>$v){$Arrmd5[]=md5($v['url']);}
	       	  $Wmd5['md5']=array('in',$Arrmd5);	
	       	  if(!$this->UrlDB->where($Wmd5)->delete()) {
	       	  	 echo $this->UrlDB->getDBError();
	       	    $this->error=$this->UrlDB->getDBError();
	       	  	return false;
	       	  }
	       	}
	       	$this->ContDB->where($where)->delete();
	       	return true;
	       }else{
	       	$this->error='请选择要删除的资源';
	       	return false;
	       }
	  	 }else if($act=='delall'){
	  	   $this->ContDB->where('1=1')->delete();
		   $this->UrlDB->where('1=1')->delete();
	  	   return true;	
	  	 }else if($act=='del'){
	  	   $this->ContDB->where('id='.$_GET['vid'])->delete();
	  	   return true;	
	  	 }
	       return false;
		}
		
	
}
?>