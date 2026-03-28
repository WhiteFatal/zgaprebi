function hide_popup(Id){
	$('#'+Id).fadeOut('fast');
	$('.popup').fadeOut('fast');
	$('.popup').children().children('form').trigger("reset");
	$('.popup').children().children('form').children('input').removeAttr('disabled');
	$('.popup').children().children('form').children('.status').hide("reset");
	$('.popup').children().children('form').children('.price').children('span.cash').text(0);
}

function show_popup(Id,title,text){
	$('.popup').fadeIn('fast');
	$('#'+Id).fadeIn('fast');	
	if(Id=='confirm'){
		$('#confirm h1').text(title);
		$('#confirm .text').text(text);
	}
}

function showListing(li, i){
	setTimeout(function(){
		li.css({opacity:1});
	}, (i)*50);
}

$(document).ready(function(e) {
	if(document.location.href.indexOf('home/#auth_error') > -1) {
        show_popup("confirm","ავტორიზაციის შეცდომა","ბალანსის შესავსებად აუცილებელია გაიაროთ ავტორიზაცია!");
    }
	
	if(document.location.href.indexOf('home/#amount_error') > -1) {
        show_popup("confirm","ბალანსის შევსება","თანხა მითითებულია არასწორად!");
    }
	
	$('input#amount').numeric({
		allowPlus	:	false,
		allowMinus	:	false,
		allowThouSep:	false,
		allowDecSep	: 	true,
		maxDigits	:	4
	});
	
	$('input#gold').numeric({
		allowPlus	:	false,
		allowMinus	:	false,
		allowThouSep:	false,
		allowDecSep	: 	false,
		maxDigits	:	4
	});
	
	$('input#amount, input#gold').keyup(function(){
		var Id = $(this).attr('id');
		var val = $(this).val();
		
		if(Id=='amount'){
			var gold = parseFloat(val) / parseFloat(0.1);
			gold = Math.round(gold * 100) / 100;
			gold = Math.floor(gold);
			if(val=='') gold = '';
			$('input#gold').val(gold);
		}else{
			var amount = parseInt(val) * parseFloat(0.1);
			amount = Math.floor(amount * 100) / 100;
			if(val=='') amount = '';
			$('input#amount').val(amount);
		}
	});
	
	var user_menu = 0;
	$('#user').click(function(){
		if(user_menu==0){
			$('span.uid').stop().fadeTo('fast',1)
			$('#user_menu').stop().slideDown('fast');
			user_menu = 1;
		}else{
			$('#user_menu').stop().slideUp('fast');
			$('span.uid').stop().fadeTo('fast',0); 
			user_menu = 0;
		}
	});
	
	GeoKBD.map('search-form','keyword');
	
	$(".full_desc, ul#searched").niceScroll({
		cursorborder:"none", 
		scrollspeed:40,
		cursorcolor:"#391a0a"
	});
	
	$('.purchase-btn').click(function(){
		var type = $(this).attr('id');
		
		if(type=='price_four'){
			var status = 'დარწმუნებული ხართ, რომ გსურთ ზღაპრის მოსმენა? ზღაპარი ხელმისაწვდომი იქნება 4 საათის განმავლობაში!';	
		}else{
			var status = 'დარწმუნებული ხართ, რომ გსურთ ზღაპრის შეძენა? ზღაპარი ხელმისაწვდომი იქნება ყოველთვის!';
		}
		
		if(confirm(status)===true){
			$('#buy-form button[type=button]').attr('disabled','disabled');
			$.post($('#buy-form').attr('action'),{buy:1,type:type},function(data){
				if(data.res == 'success'){
					$('.player').html(data.html);
					$('audio').audioPlayer();
					hide_popup('buy');
					$('.audioplayer-playpause').trigger('click');
				}else{
					$('#buy-form button[type=button]').removeAttr('disabled');
					$('#buy').children('.status').text(data.txt).show('fast');
				}
			},"JSON");
			return false;
		}
	});
	
	$('.start-premium-btn').click(function(){
		var type = $(this).attr('data-Id');
		
		if(confirm("დარწმუნებული ხართ, რომ გსურთ პრემიუმ პაკეტის ჩართვა?")===true){
			$('#premium-form button[type=button]').attr('disabled','disabled');
			$.post($('#premium-form').attr('action'),{premium:1,type:type},function(data){
				if(data.res == 'success'){
					$('.player').html(data.html);
					$('audio').audioPlayer();
					hide_popup('premium');
					show_popup('confirm','პრემიუმ პაკეტი',data.txt);
				}else{
					$('#premium-form button[type=button]').removeAttr('disabled');
					$('#premium').children('.status').text(data.txt).show('fast');
				}
			},"JSON");
			return false;
		}
	});
	
	$('#contact-form').submit(function(){
		$('#contact-form input[type=submit]').attr('disabled','disabled');
		$.post($('#contact-form').attr('action'),$(this).serialize(),function(data){
			if(data.res == 'success'){
				$('#contact-form textarea').val('');
				$('#contact-form input[type=submit]').removeAttr('disabled');
				hide_popup('registration',false,false);
				show_popup('confirm','კონტაქტი',data.txt);
			}else{
				$('#contact-form').children('.status').text(data.txt).show('fast');
				$('#contact-form input[type=submit]').removeAttr('disabled');
			}
		},"JSON");
		return false;
	});
	
	$('#balance-form').submit(function(){
		$('#balance-form input[type=submit]').attr('disabled','disabled');
		$.post($('#balance-form').attr('action'),$(this).serialize(),function(data){
			if(data.res == 'success'){
				$('#balance-form input').attr('disabled','disabled');
				document.location.href = data.url;
				hide_popup('settings',false,false);
				show_popup('confirm','პარამეტრები',data.txt);
			}else{
				$('#balance-form').children('.status').text(data.txt).show('fast');
				$('#balance-form input[type=submit]').removeAttr('disabled');
			}
		},"JSON");
		return false;
	});
	
	$('#settings-form').submit(function(){
		$('#settings-form input[type=submit]').attr('disabled','disabled');
		$.post($('#settings-form').attr('action'),$(this).serialize(),function(data){
			if(data.res == 'success'){
				$('#settings-form input').attr('disabled','disabled');
				hide_popup('settings',false,false);
				show_popup('confirm','პარამეტრები',data.txt);
			}else{
				$('#settings-form').children('.status').text(data.txt).show('fast');
				$('#settings-form input[type=submit]').removeAttr('disabled');
			}
		},"JSON");
		return false;
	});
	
	$('#registration-form').submit(function(){
		$('#registration-form input[type=submit]').attr('disabled','disabled');
		$.post($('#registration-form').attr('action'),$(this).serialize(),function(data){
			if(data.res == 'success'){
				$('#registration-form input').attr('disabled','disabled');
				hide_popup('registration',false,false);
				show_popup('confirm','რეგისტრაცია',data.txt);
			}else{
				$('#registration-form').children('.status').text(data.txt).show('fast');
				$('#registration-form input[type=submit]').removeAttr('disabled');
			}
		},"JSON");
		return false;
	});
	
	$('#recovery-form').submit(function(){
		$('#recovery-form input[type=submit]').attr('disabled','disabled');
		$.post($('#recovery-form').attr('action'),$(this).serialize(),function(data){
			if(data.res == 'success'){
				$('#recovery-form input').attr('disabled','disabled');
				hide_popup('recovery',false,false);
				show_popup('confirm','პაროლის აღდგენა',data.txt);
			}else{
				$('#recovery-form').children('.status').text(data.txt).show('fast');
				$('#recovery-form input[type=submit]').removeAttr('disabled');
			}
		},"JSON");
		return false;
	});
	
	$('#authorisation-form').submit(function(){
		$('#authorisation-form input[type=submit]').attr('disabled','disabled');
		$.post($('#authorisation-form').attr('action'),$(this).serialize(),function(data){
			if(data.res == 'success'){
				$('#authorisation-form input').attr('disabled','disabled');
				location.href = '/';
			}else{
				$('#authorisation-form').parent('.authorisation').children('.status').text(data.txt).show('fast');
				$('#authorisation-form input[type=submit]').removeAttr('disabled');
			}
		},"JSON");
		return false;
	});
	
	$('audio').audioPlayer();
	
	$('.tooltip').tooltipster({
		theme: 'tooltipster-shadow',
		contentAsHTML: true
	});
	
	$('.premium_on').tooltipster({
		theme: 'tooltipster-shadow',
		position: 'right',
		contentAsHTML: true
	});
	
	$('#search-form').bind('keypress', function(e){
		if(e.which == 13) return false;
	});
	
	$('#search').keyup(function(){
		var keyword = $(this).val();
		
		if(keyword.length<4){
			if($('#search-result').is(':visible')){
				$('#search-result').stop().slideUp('fast');
				$('ul#searched').html('<li><a href="javascript:void(0);">იძებნება...</a></li>');	
			}
			return false;
		}
		
		$('ul#searched').html('<li><a href="javascript:void(0);">იძებნება...</a></li>');
		$('#search-result').stop().slideDown('fast');
		
		$.post($('search-form').attr('action'),{keyword:keyword},function(data){
			if(data.res=='success'){
				$('ul#searched').html(data.html);
				$('#search-result').stop().slideDown('fast');
			}else{
				if($('#search-result').is(':visible')){
					$('ul#searched').html('<li><a href="javascript:void(0);">არაფერი მოიძებნა!</a></li>');	
				}
			}
		},"JSON");
		return false;
	});
	
	$('.arrow').click(function(e){
		e.preventDefault();
		if($('.arrow').hasClass('disabled')==true) return false;
		var direction = $(this).attr('id');
		var type = $(this).parent().attr('class');
		var page = $(this).parent().children('.pages').children('span#from').text();
		var maxPage = $(this).parent().children('.pages').children('span#to').text();
		
		if(direction=='next'){
			var pg = parseInt(page)+1;
			if (pg > maxPage) return false;
		}else{
			var pg = parseInt(page)-1;
			if (pg < 1) return false;
		}
		
		$('.arrow').addClass('disabled');
		
		if(type=='purchased'){
			var url = '/purchased';
		}else{
			var url = '/tales';
		}
		
		$.post(url,{page:pg,type:type},function(data){
			if(data.res=='success'){
				$('ul.'+type).html(data.html);
				$('.'+type).children('.pages').children('span#from').text(pg);
				$('.arrow').removeClass('disabled');
				$('.tooltip').tooltipster({
					multiple:true,
					theme: 'tooltipster-shadow',
					contentAsHTML: true
				});
				for (var i=0; i<$('ul.'+type+' li').length; i++){
					li = $('ul.'+type+' li:eq('+i+')');
					showListing(li, i);
				}
			}else{
				$('.arrow').removeClass('disabled');
				alert(data.txt);	
			}
		},"JSON");
		return false;
	});
	
	$('.audioplayer-playpause').click(function(){
		var Id = $(this).closest('.audioplayer').children('audio').attr('data-Id');
		$.post("/tale/"+Id,{tale_listen:'listen'});
	});
	
	window.fbAsyncInit = function() {
		FB.init({
			appId      : '790277907718676',
			xfbml      : true,
			version    : 'v2.2'
		});
	};

	(function(d, s, id){
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) {return;}
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/ka_GE/sdk.js";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
	
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
	ga('create', 'UA-59989050-1', 'auto');
	ga('send', 'pageview');
});

$(document).click(function(e) {
    if(e.target.id == "search") return false;
	if($('#search-result').is(':visible')){
		$('#search-result').stop().slideUp('fast');	
	}
});