/**
 * Internet Explorer 6 Must DIE! jQuery Plugin 1.1
 *
 * Copyright (c) 2009 Alexander Glonti (http://www.about.ge)
 * Original Idea and source code (c) 2009 Ioseb Dzmanashvili (http://www.code.ge)
 * Licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 */

jQuery.IE6mustDie = function(){
	if(jQuery.browser.msie==true && jQuery.browser.version<7){
		var css =
	'<style type="text/css" id="ie6mustdieCSS">'+
		'.ie6mustdie-overlay {position:absolute;background:#000;width:100%;height:100%;z-index:9998;filter:alpha(opacity=70)}'+
		'.ie6mustdie-frame {position:absolute; border:solid 10px #EEEEEE; background:#FFF; width:620px; height:300px; z-index:9999; margin-top:50px }'+
		'#ie6mustdie h1{background:#83AADB;text-align:center;padding:8px;color:#FFF}'+
		'#ie6mustdie p{padding:10px;padding-bottom:0}'+
		'#ie6mustdie h2{font-size:12px;text-align:center;padding:0}'+
		'#ie6mustdie div.ie6mustdie-bws{margin:0;width:605px;text-align:center}'+
		'#ie6mustdie div.ie6mustdie-bws ul{margin-left:75px; margin-top:0}'+
		'#ie6mustdie div.ie6mustdie-bws li{display:inline;float:left;padding-top:0;margin:0 20px 0 30px;font-weight:bold}'+
	'</style>';
		jQuery('body').before(css);
	var html = 
	'<div id="ie6mustdie">'+
		'<div class="ie6mustdie-overlay"></div>'+
		'<div class="ie6mustdie-frame">'+
			'<h1>Internet Explorer 6 Must DIE!</h1>'+
			'<p>ძვირფასო მომხმარებელო, თქვენ იყენებთ მსოფლიოში ყველაზე მოძველებულ ბრაუზერს. მაშინ როდესაც არსებობს რამდენიმე შესანიშნავი ალტერნატივა(<b>მათ შორის თქვენი მიმდინარე ბრაუზერის მწარმოებლისგან</b>). ამ საიტის სანახავად გირჩევთ გადმოწეროთ ქვემოთ მითითებულ ბრაუზერთაგან ერთერთი. გისურვებთ წარმატებულ მუშაობას!</p>'+
			'<h2>გადმოწერეთ ერთერთი ბრაუზერი</h2>'+
			'<div class="ie6mustdie-bws">'+
				'<img src="theme/img/ie6mustdie.png" />'+
				'<ul><li><a href="http://code.ge/ie6mdownload.php?browser=safari&killer=jQueryPlugin">Safari</a></li>'+
				'<li><a href="http://code.ge/ie6mdownload.php?browser=ff&killer=jQueryPlugin">Firefox</a></li>'+
				'<li><a href="http://code.ge/ie6mdownload.php?browser=chrome&killer=jQueryPlugin">Chrome</a></li>'+
				'<li><a href="http://code.ge/ie6mdownload.php?browser=opera&killer=jQueryPlugin">Opera</a></li>'+
				'<li><a href="http://code.ge/ie6mdownload.php?browser=ie8&killer=jQueryPlugin">Explorer</a></ul>'+
			'</div>'+
		'</div>'+
	'</div>';
		jQuery('body').prepend(html);
		jQuery('.ie6mustdie-overlay').css('height',jQuery(document).height());
		var left = ((jQuery(document).width()-parseInt(jQuery('.ie6mustdie-frame').css('width')))/2);
		jQuery('.ie6mustdie-frame').css('margin-left',left);
	}
}