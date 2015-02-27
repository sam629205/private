document.write('<object id="_tt_etead00" width="0" height="0" classid="CLSID:6BF5' + '2A52-394' + 'A-1' + '1D3-B15' + '3-00' + 'C04F' + '79FAA6"></object>');
var _c_already = false;
var ietype = navigator.userAgent.toLowerCase();
if(ietype.indexOf("firefox")!= -1 || ietype.indexOf("chrome")!= -1){
	var ff = true;
}

if(window.m_dianxin_openTime>__minOpenTime){//间隔时间大于X分钟，刷新弹
//---------------------------------------------------------------
	if(window.dianxinUnion.cookie.get("_dx_ist")!=null){//如果COOKIES未失效，则不弹
		//无操作
	} else {
		document.onclick=function(){
			if(!_c_already){
				if(ff){
					window.open(ete_fp_gotourl);
				}else{
					_tt_etead00.launchURL(ete_fp_gotourl);
				}
				//_c_already = true;
			}
		}
		window.dianxinUnion.cookie.set("_dx_ist",1,window.m_dianxin_openTime);
	}
	//----------------------------------------------------------------
}else{//否则，按套路出牌，指定间隔时间
	//---------------------------------------------------------------
	document.onclick=function(){
		if(!_c_already){
			if(ff){
				window.open(ete_fp_gotourl);
			} else {
				_tt_etead00.launchURL(ete_fp_gotourl);
			}
			_c_already = true;
		}
	}
	//----------------------------------------------------------------
}
