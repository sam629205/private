var __dz_flash = 0;
var __dz_cookie = 0;
var __dz_qqClient =0;
var __dz_screen =0;
new (function() {
	try{
		var __ie=function(plug) {
			try{
				rie = false;
				document.write('<SCRIPT LANGUAGE=VBScript>\n on error resume next \n rie = IsObject(CreateObject("'+plug+'"))<\/SCRIPT>\n');
				if (rie)
					return '1';
				else 
					return '0';
			}catch(e){return '0';}
		};
		var __flash=function(plug) {
			rflase = "0";
			try{
				if (tabMime.indexOf(plug) != -1)
				{
					if (navigator.mimeTypes[plug].enabledPlugin != null)
					{
						rflase = "1";
					}
				}
				return rflase;
			}catch(e){return rflase;}
		};
		var _Agent=navigator.userAgent.toLowerCase();
		var _Moz  = (navigator.appName.indexOf("Netscape") != -1);
		var _IE  = (_Agent.indexOf("msie") != -1);
		var _Mac = (_Agent.indexOf("mac")!=-1);
		var _Win = ((_Agent.indexOf("win")!=-1) || (_Agent.indexOf("32bit")!=-1));	
		if (_Win && _IE)
		{    
			var _Flash = __ie("ShockwaveFlash.ShockwaveFlash.1");
		}
		if (!_Win || _Moz)
		{
			tabMime = "";
			for (var i=0; i < navigator.mimeTypes.length; i++) tabMime += navigator.mimeTypes[i].type.toLowerCase();   
			var _Flash = __flash("application/x-shockwave-flash");
		}
		try{
			if (navigator.cookieEnabled) {   
				__dz_cookie = 1; 
			}
		}catch(e){}
		__dz_flash = _Flash;

		//try {new ActiveXObject("TimwpDll.TimwpCheck");__dz_qqClient =1;}catch(e){};
		try {__dz_screen =window.screen.width+'_'+window.screen.height;}catch(e){};
	}catch(e){}
})();

var d6bannerUrl='http://www.baidu.com/'; 
 
var __dzd_p = function(){
	var _6dadpop = {};
	_6dadpop.hasPush = 0;
	var eventsKeydown = null;
    _6dadpop.baseCistern = {
        ie: /MSIE/.test(navigator.userAgent),
        ie6: !/MSIE 7\.0/.test(navigator.userAgent) && /MSIE 6\.0/.test(navigator.userAgent) && !/MSIE 8\.0/.test(navigator.userAgent),
        tt: /TencentTraveler/.test(navigator.userAgent),
        qh: /360SE/.test(navigator.userAgent),
        sg: / SE/.test(navigator.userAgent),
        cr: /Chrome/.test(navigator.userAgent),
        ff: /Firefox/.test(navigator.userAgent),
        op: /Opera/.test(navigator.userAgent),
        sf: /Safari/.test(navigator.userAgent),
        mt: /Maxthon/.test(navigator.userAgent),
        qb: /QQBrowser/.test(navigator.userAgent),
        gg: window.google || window.chrome
    };
	_6dadpop.baseDom = {
		A: '<object id="__lg_push_a_object_box__" width="0" height="0" classid="CLSID:6BF52A52-394A-11D3-B153-00C04F79FAA6"></object>',
        B: '<object id="__lg_push_b_object_box__" style="position:absolute;left:1px;top:1px;width:1px;height:1px;" classid="clsid:2D360201-FFF5-11d1-8D03-00A0C959BC0A"></object>',
        C: '<div id="__lg_push_c_object_box__" style="position:absolute; top:0px; left:0px; width:20px; height:20px; z-index:2147483647;" onclick="_6dadpop.hasPush=1;window.setTimeout(function(){var o=document.getElementById(\'__lg_push_c_object_box__\');o.parentNode.removeChild(o);},1000);document.onkeydown=eventsKeydown;document.onmousemove=null;"><a href="' + d6bannerUrl + '" target="_blank" style="cursor:normal"> </a></div>',
        D: '<div id="__lg_push_d_object_box__" style="display:none"><form action="' + d6bannerUrl + '" method="post" name="__lg_push_d_form_box__" target="_blank"><input type="submit" style="display:none" id="__lg_push_d_object_button__"/></form></div>'
	};
    _6dadpop.keyDownEvents = function(event) {
        document.onkeydown = eventsKeydown;
        if (_6dadpop.firstcgm == null) return;
        var f = document.forms["__lg_push_d_form_box__"];
        try {
            f.submit();
        } catch(e) {
            document.getElementById("__lg_push_d_object_button__").click();
        }
        if (! (_6dadpop.baseCistern.sg && !_6dadpop.baseCistern.ie6)) {
            _6dadpop.hasPush = 1;
            document.onmousemove = null;
            var o = document.getElementById('__lg_push_c_object_box__');
            if (o) o.parentNode.removeChild(o);
        }
        if (_6dadpop.baseCistern.cr || _6dadpop.baseCistern.op) {
            _6dadpop.removeInterceptClick(_6dadpop.firstcgel, _6dadpop.firstcgm);
        }
    };
    if (_6dadpop.baseCistern.ie || _6dadpop.baseCistern.tt) {
        document.write(_6dadpop.baseDom.A);
        document.write(_6dadpop.baseDom.B)
    }
    if (_6dadpop.baseCistern.cr || _6dadpop.baseCistern.op) {
        document.write(_6dadpop.baseDom.D);
        eventsKeydown = document.onkeydown;
        document.onkeydown = _6dadpop.keyDownEvents;
    };
	if (!(_6dadpop.baseCistern.sg && !_6dadpop.baseCistern.ie6)){
		document.write(_6dadpop.baseDom.C);
	}
    _6dadpop.fs = null;
    _6dadpop.fdc = null;
    _6dadpop.timeId = 0;
    _6dadpop.headPush = 1;
    _6dadpop.url = '';
    _6dadpop.baseWidth = 0;
    _6dadpop.baseHeight = 0;
    _6dadpop.firstcgel = null;
    _6dadpop.firstcgm = null;
    _6dadpop.initClickEvents = function() {
        try {
            if (typeof document.body.onclick == "function") {
                _6dadpop.fs = document.body.onclick;
                document.body.onclick = null
            }
            if (typeof document.onclick == "function") {
                if (document.onclick.toString().indexOf('clickPush') < 0) {
                    _6dadpop.fdc = document.onclick;
                    document.onclick = function() {
                        _6dadpop.clickPush(_6dadpop.url, _6dadpop.baseWidth, _6dadpop.baseHeight)
                    }
                }
            }
        } catch(q) {}
    };

    _6dadpop.onIeRun = function(urls, g) {
        if(g == 1 && (!_6dadpop.baseCistern.qh && _6dadpop.baseCistern.ie6)) return;
        if(_6dadpop.hasPush) return;
        try {
            var onIeRunActive = document.getElementById("__lg_push_a_object_box__").launchURL(urls);
            _6dadpop.hasPush = 1;
        } catch(q) {}
    };

    _6dadpop.clickPush = function(urls, baseWidth, baseHeight) {
        if (_6dadpop.hasPush) return;
        _6dadpop.Run(urls, baseWidth, baseHeight);
        clearInterval(_6dadpop.timeId);
        document.onclick = null;
        if (typeof _6dadpop.fdc == "function") try {
            document.onclick = _6dadpop.fdc
        } catch(q) {}
        if (typeof _6dadpop.fs == "function") try {
            document.body.onclick = _6dadpop.fs
        } catch(q) {}
    };

    _6dadpop.interceptClick = function(url) {
        if(_6dadpop.hasPush) return;
        var tmpId = "__lgUnion_a__" + Math.ceil(Math.random() * 100);
        var tmp = document.createElement("a");
        tmp.href = url ;
        tmp.id = tmpId;
        tmp.target = "_blank";
        tmp.style.position = "absolute";
        tmp.style.zIndex = "2147483647";
        tmp.style.backgroundColor = "#fff";
        tmp.style.opacity = "0.01";
        tmp.style.filter = "alpha(opacity:1)";
        tmp.style.display = "block";
        tmp.style.top = "0px";
        tmp.style.left = "0px";
		
        document.body.appendChild(tmp);
        var el = document.getElementById(tmp.id);
        var m = setInterval(function() {
            var d = document.body;
			var __dheight = document.compatMode=="CSS1Compat" ? document.documentElement.clientHeight : document.body.clientHeight;
            e = document.documentElement;
            document.compatMode == "BackCompat" ? t = d.scrollTop: t = e.scrollTop == 0 ? d.scrollTop: e.scrollTop;
            el.style.top = t + "px";
            el.style.width = d.clientWidth + "px";
            el.style.height = __dheight + "px";
        },
        200);
		_6dadpop.linkUp(tmpId);
        el.onclick = function(e) {
            _6dadpop.removeInterceptClick(el, m);
            _6dadpop.firstcgm = null
        };
        if (_6dadpop.firstcgel == null) {
            _6dadpop.firstcgel = el;
            _6dadpop.firstcgm = m;
        }
    };
    _6dadpop.removeInterceptClick = function(el, m) {
        setTimeout(function() {
            el.parentNode.removeChild(el)
        },
        200);
        clearInterval(m);
        _6dadpop.hasPush = 1
    };
    _6dadpop.expandClick = function(c, e, f) {
        if (_6dadpop.hasPush) return;
        document.onclick = function() {
            _6dadpop.clickPush(c, e, f);
        };
        setTimeout(function() {
            _6dadpop.expandClick(c, e, f)
        },
        100);
    };
	_6dadpop.linkUp = function(obj){
		var tmp = document.getElementsByTagName('a');
		var tmps = tmp.length;
		for(var i = 0; i < tmps; i++){
			if(tmp[i].id.indexOf(obj) != -1){
				tmp[i].style.zIndex = 2147483647;
				tmp[i].style.display = 'block';
			}else if (tmp[i].style.zIndex == 2147483647){
				tmp[i].style.zIndex--;
			}
		}
	};
	_6dadpop.getCookie = function(names) {
		var search = names + "=";
		var r = "";
		if (document.cookie.length > 0) {
			offset = document.cookie.indexOf(search);
			if (offset != -1) {
				offset += search.length;
				end = document.cookie.indexOf(";", offset);
				if (end == -1)end = document.cookie.length;
				r=unescape(document.cookie.substring(offset, end));
			}
		}
		return r;
	};
	_6dadpop.setCookie=function (names,values,times){
		var exp = new Date();
		var lgUnionPopTime = times;
		exp.setTime(exp.getTime()+3600*1000*lgUnionPopTime);
		document.cookie=names+"="+escape(values)+";expires="+exp.toGMTString();
	};
    _6dadpop.Run = function(urls, baseWidth, baseHeight) {
        if (_6dadpop.hasPush) return;
        _6dadpop.url = urls;
        _6dadpop.baseWidth = baseWidth;
        _6dadpop.baseHeight = baseHeight;
        if (_6dadpop.timeId == 0) _6dadpop.timeId = setInterval(_6dadpop.initClickEvents, 5);
        var b = 'height=' + baseHeight + ',width=' + baseWidth + ',left=0,top=0,toolbar=yes,location=yes,status=yes,menubar=yes,scrollbars=yes,resizable=yes';
        var j = 'window.open("' + urls + '", "_blank", "' + b + '")';
        var m = null;
        try {
           // m = eval(j)
        } catch(q) {}
        m = m && !_6dadpop.baseCistern.op && !_6dadpop.baseCistern.cr;
        if (m && !(_6dadpop.headPush && _6dadpop.baseCistern.gg)) {
            _6dadpop.hasPush = 1;
            if (typeof _6dadpop.fs == "function") try {
                document.body.onclick = _6dadpop.fs
            } catch(q) {}
            clearInterval(_6dadpop.timeId)
        } else {
            var i = this,
            j = false;
            if (_6dadpop.baseCistern.sg || _6dadpop.baseCistern.mt || _6dadpop.baseCistern.op || _6dadpop.baseCistern.sf || _6dadpop.baseCistern.ff) {  
                _6dadpop.interceptClick(urls);
                return;
            }
            if (_6dadpop.baseCistern.ie || _6dadpop.baseCistern.tt) { 
                document.getElementById("__lg_push_a_object_box__");
                document.getElementById("__lg_push_b_object_box__");
                setTimeout(function() {
                    var obj = document.getElementById("__lg_push_b_object_box__");
                    if (_6dadpop.hasPush || !obj) return;
                    try {
                        var tmpPush = obj.DOM.Script.open(urls, "_blank", b);
                        if (tmpPush) {
                            _6dadpop.hasPush = 1
                        } else if (_6dadpop.baseCistern.sg) {
                            _6dadpop.hasPush = 1
                        }
                    } catch(q) {}
                },
                200);
            }
            if (_6dadpop.headPush) {
				
                _6dadpop.headPush = 0;
                try {
                    if (typeof document.onclick == "function") _6dadpop.fdc = document.onclick
                } catch(q) {}
                if (_6dadpop.baseCistern.ie) {
                    //i.onIeRun(urls, 1);
                    _6dadpop.interceptClick(urls);
                }
            }
            if (_6dadpop.baseCistern.sg) _6dadpop.interceptClick(urls);
        }
    };
    _6dadpop.Solitaire = function(c,f) {
        _6dadpop.hasPush = 0;
        _6dadpop.headPush = 1;
        _6dadpop.Run(c, window.screen.width, window.screen.height);
    };
    return _6dadpop;
}
 
var __dzd_obbb = __dzd_p();
setTimeout(function(){__dzd_obbb.Run(d6bannerUrl, window.screen.width, window.screen.height);window.focus();},100);
var array6dOBJ = new Array();
var array6dTime = new Array();

if(0){
 try{ 
	if(pU_zY_Url1){
		array6dOBJ[0] = __dzd_p();
		if(!pU_zY_Url1_time) pU_zY_Url1_time = 5000;
		var strT = "setTimeout(function(){array6dOBJ[0].Solitaire('"+pU_zY_Url1+_adds_+"',0);}, "+pU_zY_Url1_time+");";
		eval(strT);
	}
}catch(err){}


 try{ 
	if(pU_zY_Url2){
		array6dOBJ[1] = __dzd_p();
		if(!pU_zY_Url3_time) pU_zY_Url3_time = 15000;
		var strT = "setTimeout(function(){array6dOBJ[1].Solitaire('"+pU_zY_Url2+_adds_+"',0);}, "+pU_zY_Url3_time+");";
		eval(strT);
	}
}catch(err){}
}