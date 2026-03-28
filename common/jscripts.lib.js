/*
GeoKBD - Georgian Keyboard
v 1.0
Dual licensed under the MIT and GPL licenses.
----------------------------------------------------------------------------
Copyright (C) 2009 Ioseb Dzmanashvili
http://www.code.ge/geokbd
*/

eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(8(){P.Z.1a=8(a){a.20();3(u.U){5 b=u.U.1b();3(b){b.D=4}}9 3(a.1v!=V){21(a){5 c=1w,10=1v,1x=22}5 d=a.O.1c(0,10)+4+a.O.1c(1x,a.O.z);a.O=d;a.1w=c;a.1y(10+4.z,10+4.z)}9{a.O+=4;a.1y(a.O.z,a.O.z)}};P.Z.1d=8(){5 a,11,D=[],1z="23";E(5 i=0;i<4.z;i++){11=4.1c(i,1);3((a=1z.Q(11))>=0){D.R(P.1e(a+24))}9{D.R(11)}}w D.1f(\'\')};8 $(g,h,l,m,n){5 o=4.W=[];h=h||u.B;3(g&&(g.1A||X g==\'25\')){4.W.R(g)}9 3(!g||X g==\'12\'||n){(8(a){5 b=[];3(a){b=a.1g(/ /g,\'\').1g(/\\./g,\'26.\').1g(/#/g,\'1h.\').1B(\',\')}E(5 i=0;i<h.z;i++){5 c=h[i];x(c).1i(1j r());c.1C(l,m);E(5 j=0;j<h[i].13.z;j++){5 d=c.13[j];3(d.1D&&/D|1E/i.1k(d.1D)){3(b.z){5 e=F;E(5 k=0;k<b.z;k++){5 f=b[k].1B(\'.\');3(e=(f.z==1&&d.1l==f[0])||(f.z==2&&d[f[0]]==f[1])){1F}}3(e&&!n){d.y=M;e=F}9 3(!e&&n){d.y=M}}9{d.y=M}}}}})(g||n)}w 4}$.Z={1G:8(a){3(a){E(5 i=0;i<4.W.z;i++){a.27(4,4.W[i],i)}}w 4},1i:8(){5 p,1m=28;4.1G(8(a){E(5 i=0;i<1m.z;i++){3((p=1m[i])!=A){E(5 b 1H p){3(a==p){29}9 3(p[b]!=V){a[b]=p[b]}}}}});w 4},1I:8(a){w 4.W[a||0]||A}};8 x(a,b,c,d,e){w 1j $(a,b,c,d,e)};x.G=8(e){5 b=8(a){4.2a=e};b.Z={14:8(){w 4.1J||4.2b},S:8(){w 4.2c||4.2d},2e:8(a){5 t=4.S();t.1K.1L()==a?t:A},1n:8(){1M{e.2f();e.2g()}1N(2h){e.2i=M;e.2j=F}}};w x(1j b()).1i((e||J.G)).1I()};x.G.1o=8(o,a,b){3(o.1O){o.1O(a,b,F)}9 3(o.1P){w o.1P(\'15\'+a,b)}9{o[\'15\'+a]=b}};x.G.2k=8(o,a,b){3(o.1Q){o.1Q(a,b,F)}9 3(o.1R){o.1R(\'15\'+a,b)}9{o[\'15\'+a]=A}};5 q=2l.2m.1L();x.T={2n:8(a){q.Q(a)>-1},1S:q.Q(\'2o\')>-1,Y:q.Q(\'1p\')>-1,2p:q.Q(\'1p 6\')>-1,2q:q.Q(\'1p 7\')>-1};5 r=8(){4.y=M;4.v=A;4.16=M;4.1C=8(a,b){4.16=!!!a;3(!4.16){a=a||s.17}9{a=a||s.N}b=b||\'2r\';3(4.13[b]){4.v=4.13[b]}9{4.v={};4.v.C=a==s.17}4.y=4.v.C},4.K=8(a){3(4.v){3(a==V){4.v.C=a=!4.v.C}9{4.v.C=a}}4.y=!!a},4.1T=8(e){e=x.G(e);3(e.1U||e.1V||e.2s)w;3(!x.T.Y&&!x.T.1S&&!e.2t){w}5 a=e.S();3(a.y!=V&&a.y){5 b=e.14();3(b==1q){4.K();w F}3(!4.v.C)w;5 c=P.1e(b);5 d=c.1d();3(d!=c){3(x.T.Y){J.G.1J=d.2u(0)}9{d.1a(a);w F}}}}};5 s={17:\'y\',2v:\'2w\',N:\'y\',2x:8(c,d){8 K(a){E(5 i=0;i<u.B.z;i++){5 b=u.B[i];3(b.16&&b.K&&!b.v.1r){b.K(a.C)}}};4.N=c;d=u.1W(d)||{};3(!d.1A){d.C=c==s.17}9{d.2y=8(){K(4)}}u.1T=8(e){e=x.G(e);5 a=/2z|1E/i.1k(e.S().1r);3(e.14()==1q&&(!a||(a&&!e.S().H.v.1r))){d.C=!d.C;K(d);w F}}},2A:8(a){1M{5 b=u.U?u.U.1b().D:u.2B();b.1a(a)}1N(e){}w F},1s:8(a){5 b,I,v,N,L;3(a){b=a.B||A;I=a.I||A;v=a.v||A;N=a.N||A;L=a.L||A}5 c=[],B=[];3(b){3(b.1t){3(b.1t==P){c.R(b)}9 3(b.1t==2C){c=b}3(c.z){E(5 d 1H c){3(u.B[c[d]]){B.R(u.B[c[d]])}}}}9{B.R(b)}}9{B=u.B}3(I){I=X I==\'12\'?I:I.1f(\',\')}3(L){L=X L==\'12\'?L:L.1f(\',\')}x(I,B,N,v,L)},2D:8(a,b,c,d){4.1s({B:a,I:b,v:d,N:c})},2E:8(a,b){4.1s(A,a,A,b)},2F:8(f){5 g=8(e){e=x.G(e);3(e.1U||e.1V)w;5 a=e.S().2G;3(a.y==V)a.y=M;5 b=e.14(),D=P.1e(b),H=2H.u.B[a.1u];3(b==1q){a.y=!a.y;3(H&&H.K)H.K(a.y);e.1n()}9 3(H&&H.v){a.y=H.v.C}H=A;3(a.y){5 c=D.1d();3(c!=D){3(!x.T.Y){a.2I(\'2J\',F,c)}9{5 d=a.U.1b();d.2K(c)};e.1n()}}};5 h=8(e){x.G.1o(4.18.u,\'1X\',g);4.1Y=A};5 i=J.2L(8(){5 a=X f==\'12\'?u.1W(f):f();3(a){E(5 p=a.1Z;p&&p!=u.2M;p=p.1Z){3(/H/i.1k(p.1K)){3(a.18.u){a.18.u.1u=p.1l||p.1h}9{a.u.1u=p.1l||p.1h}1F}}3(!x.T.Y){x.G.1o(a.18.u,\'1X\',g,M)}9{a.1Y=h}a=A;J.2N(i)}},0)},2O:8(a){5 b=J.19;3(!J.19){J.19=a}9{J.19=8(){b();a()}}}};J.2P=s})();',62,176,'|||if|this|var|||function|else|||||||||||||||||||||document|switcher|return|fn|ka|length|null|forms|checked|text|for|false|event|form|fields|window|changeLang|excludeFields|true|lang|value|String|indexOf|push|getTarget|browser|selection|undefined|els|typeof|isIe|prototype|start|chr|string|elements|getKeyCode|on|global|KA|contentWindow|onload|pasteTo|createRange|substr|translateToKA|fromCharCode|join|replace|id|extend|new|test|name|args|cancel|attach|msie|96|nodeName|map|constructor|parentForm|selectionStart|scrollTop|end|setSelectionRange|symbols|nodeType|split|init|type|textarea|break|each|in|get|keyCode|tagName|toLowerCase|try|catch|addEventListener|attachEvent|removeEventListener|detachEvent|isOpera|onkeypress|altKey|ctrlKey|getElementById|keypress|onfocus|parentNode|focus|with|selectionEnd|abgdevzTiklmnopJrstufqRySCcZwWxjh|4304|object|className|call|arguments|continue|orig|which|target|srcElement|targetIs|stopPropagation|preventDefault|ex|cancelBubble|returnValue|detach|navigator|userAgent|is|opera|isIe6|isIe7|geo|metaKey|charCode|charCodeAt|EN|en|setGlobalLanguage|onclick|input|pasteSelection|getSelection|Array|mapForm|mapFields|mapIFrame|ownerDocument|parent|execCommand|InsertHTML|pasteHTML|setInterval|body|clearInterval|ready|GeoKBD'.split('|'),0,{}));


jQuery.cookie = function(name, value, options) {
	// $.cookie('cookieName', 'value', {expires:10000});
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};

jQuery.winPopup = function(page,w,h,name){
		if (!name) name='win';
		if (!w) w=screen.width;
		if (!h) h=screen.height;
		top_val = (screen.height - h)/2 - 30;
		if (top_val < 0){ top_val	= 0; }
		left_val = (screen.width - w)/2;
	var pwin = window.open(page, name, "toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=0,width="+ w +",height="+ h +", top="+ top_val +"px,left="+ left_val + "px");
	if(pwin) pwin.focus();
};

jQuery.random = function(string_length){
	if (!string_length) string_length=8;
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var randomstring = '';
	for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum,rnum+1);
	}
	return randomstring;
};

jQuery.go = function(url){
	document.location = url;
};

/*
(function(jQuery) { 
	 ფორმის შევსების სტატუსი
 	jQuery.fn.formstatus = function(status){
		jQuery(this).html(status);
		jQuery(this).slideDown();
	}
})(jQuery);
*/

jQuery.share_fb = function(l){
	jQuery.winPopup('http://www.facebook.com/sharer.php?u='+l,500,500,'shareFB');
};


jQuery.fn.disableTextSelect = function() {
  return this.each(function() {
    $(this).css({
      'MozUserSelect' : 'none'
    }).bind('selectstart', function() {
      return false;
    }).mousedown(function() {
      return false;
    });
  });
};

jQuery.fn.enableTextSelect = function() {
  return this.each(function() {
    $(this).css({
      'MozUserSelect':''
    }).unbind('selectstart').mousedown(function() {
      return true;
    });
  });
};
