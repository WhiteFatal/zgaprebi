// ==========================================
// Georgian Keyboard Support (GeoKBD)
// ==========================================
(function() {
	String.prototype.pasteTo = function(el) {
		el.focus();
		if (document.selection) {
			var range = document.selection.createRange();
			if (range) { range.text = this; }
		} else if (el.selectionStart != undefined) {
			var scrollTop = el.scrollTop;
			var start = el.selectionStart;
			var end = el.selectionEnd;
			var val = el.value.substring(0, start) + this + el.value.substring(end, el.value.length);
			el.value = val;
			el.scrollTop = scrollTop;
			el.setSelectionRange(start + this.length, start + this.length);
		} else {
			el.value += this;
			el.setSelectionRange(el.value.length, el.value.length);
		}
	};

	String.prototype.translateToKA = function() {
		var e, t, result = [], map = "abgdevzTiklmnopJrstufqRySCcZwWxjh";
		for (var i = 0; i < this.length; i++) {
			t = this.substr(i, 1);
			if ((e = map.indexOf(t)) >= 0) {
				result.push(String.fromCharCode(e + 4304));
			} else {
				result.push(t);
			}
		}
		return result.join('');
	};

	GeoKBD = {
		browser: {
			isOpera: navigator.userAgent.toLowerCase().indexOf('opera') > -1,
			isIe:    navigator.userAgent.toLowerCase().indexOf('msie')  > -1,
			isIe6:   navigator.userAgent.toLowerCase().indexOf('msie 6') > -1,
			isIe7:   navigator.userAgent.toLowerCase().indexOf('msie 7') > -1
		},
		event: {
			get: function(e) { return e || window.event; },
			getKeyCode: function(e) { e = this.get(e); return e.keyCode || e.which; },
			targetIs: function(e, tag) {
				e = this.get(e);
				var n = e.target || e.srcElement;
				return n.tagName.toLowerCase() == tag ? n : null;
			},
			attach: function(el, type, fn, capture) {
				if (el.addEventListener) { el.addEventListener(type, fn, capture); }
				else if (el.attachEvent) { return el.attachEvent('on' + type, fn); }
				else { el['on' + type] = fn; }
			},
			detach: function(el, type, fn, capture) {
				if (el.removeEventListener) { el.removeEventListener(type, fn, capture); }
				else if (el.detachEvent) { el.detachEvent('on' + type, fn); }
				else { el['on' + type] = null; }
			},
			cancel: function(e) {
				e = this.get(e);
				if (e.stopPropagation) { e.stopPropagation(); e.preventDefault(); }
				else { e.cancelBubble = true; e.returnValue = false; }
			}
		},
		keyHandlers: {},
		addKeyHandler: function(e, fn) {
			if (typeof e == 'string') { e = e.charCodeAt(0); }
			if (!this.keyHandlers[e]) { this.keyHandlers[e] = []; }
			this.keyHandlers[e].push(fn);
		},
		handleKey: function(e) {
			if (GeoKBD.keyHandlers[e]) {
				var fn = null;
				for (var i = 0; i < GeoKBD.keyHandlers[e].length; i++) {
					fn = GeoKBD.keyHandlers[e][i];
					if (fn.constructor && fn.constructor == Array) { fn[0][fn[1]].call(fn[0], e); }
					else { fn(e); }
				}
			}
		},
		map: function(formId, fieldId, switcherId) {
			var self = this, formIds = [], forms = [];
			if (formId) {
				if (formId.constructor) {
					if (formId.constructor == String) { formIds.push(formId); }
					else if (formId.constructor == Array) { formIds = formId; }
					if (formIds.length) {
						for (var o in formIds) {
							if (document.forms[formIds[o]]) { forms.push(document.forms[formIds[o]]); }
						}
					}
				} else {
					forms.push(formId);
				}
			} else {
				forms = document.forms;
			}
			for (var o = 0; o < forms.length; o++) {
				var form = forms[o];
				if (!form.fields || form.fields == undefined) form.fields = {};
				if (form.ka == undefined) form.ka = form[switcherId] ? form[switcherId].checked : true;
				if (fieldId) {
					if (typeof fieldId == 'string') fieldId = [fieldId];
					for (var u = 0; u < fieldId.length; u++) {
						if (form[fieldId[u]] && !form.fields[fieldId[u]]) form.fields[fieldId[u]] = fieldId[u];
					}
				} else {
					var fname, ftype;
					for (var u = 0; u < form.elements.length; u++) {
						if (form.elements[u].type) {
							fname = form.elements[u].name || form.elements[u].id;
							ftype = form.elements[u].type.toLowerCase();
							if (fname && (ftype == 'text' || ftype == 'textarea')) { form.fields[fname] = fname; }
						}
					}
				}
				switcherId = switcherId || 'geo';
				form.switcher = switcherId;
				form.onkeypress = function(e) {
					e = self.event.get(e);
					if (e.altKey || e.ctrlKey) return;
					if (!self.browser.isIe && !self.browser.isOpera && !e.charCode) return;
					var target, sw = switcherId, keyCode = self.event.getKeyCode(e);
					if (keyCode == 96) {
						if (this[sw]) { this.ka = this[sw].checked = !this[sw].checked; }
						else { this.ka = !this.ka; }
						return false;
					} else if (this[sw]) {
						this.ka = this[sw].checked;
					}
					if (!this.ka) return;
					if (target = self.event.targetIs(e, 'textarea') || self.event.targetIs(e, 'input')) {
						if (!this.fields[target.name || target.id]) return;
						var text = String.fromCharCode(keyCode);
						var kaText = text.translateToKA();
						if (kaText != text) {
							if (GeoKBD.browser.isIe) { e.keyCode = kaText.charCodeAt(0); }
							else { kaText.pasteTo(target); return false; }
						}
					}
				};
			}
			formId = forms = formIds = null;
		}
	};

	window.GeoKBD = GeoKBD;
})();

// ==========================================
// Main Application Script
// ==========================================

function hide_popup(Id) {
	$('#' + Id).fadeOut('fast');
	$('.popup').fadeOut('fast');
	$('.popup').children().children('form').trigger('reset');
	$('.popup').children().children('form').children('input').removeAttr('disabled');
	$('.popup').children().children('form').children('.status').hide('reset');
	$('.popup').children().children('form').children('.price').children('span.cash').text(0);
}

function show_popup(Id, title, text) {
	$('.popup').fadeIn('fast');
	$('#' + Id).fadeIn('fast');
	if (Id == 'confirm') {
		$('#confirm h1').text(title);
		$('#confirm .text').text(text);
	}
}

function showListing(li, i) {
	setTimeout(function() {
		li.css({ opacity: 1 });
	}, i * 50);
}

$(document).ready(function() {

	if (document.location.href.indexOf('home/#auth_error') > -1) {
		show_popup('confirm', 'ავტორიზაციის შეცდომა', 'ბალანსის შესავსებად აუცილებელია გაიაროთ ავტორიზაცია!');
	}

	if (document.location.href.indexOf('home/#amount_error') > -1) {
		show_popup('confirm', 'ბალანსის შევსება', 'თანხა მითითებულია არასწორად!');
	}

	$('input#amount').numeric({
		allowPlus:    false,
		allowMinus:   false,
		allowThouSep: false,
		allowDecSep:  true,
		maxDigits:    4
	});

	$('input#gold').numeric({
		allowPlus:    false,
		allowMinus:   false,
		allowThouSep: false,
		allowDecSep:  false,
		maxDigits:    4
	});

	$('input#amount, input#gold').keyup(function() {
		var Id  = $(this).attr('id');
		var val = $(this).val();
		if (Id == 'amount') {
			var gold = parseFloat(val) / parseFloat(0.1);
			gold = Math.round(gold * 100) / 100;
			gold = Math.floor(gold);
			if (val == '') gold = '';
			$('input#gold').val(gold);
		} else {
			var amount = parseInt(val) * parseFloat(0.1);
			amount = Math.floor(amount * 100) / 100;
			if (val == '') amount = '';
			$('input#amount').val(amount);
		}
	});

	var user_menu = 0;
	$('#user').click(function() {
		if (user_menu == 0) {
			$('span.uid').stop().fadeTo('fast', 1);
			$('#user_menu').stop().slideDown('fast');
			user_menu = 1;
		} else {
			$('#user_menu').stop().slideUp('fast');
			$('span.uid').stop().fadeTo('fast', 0);
			user_menu = 0;
		}
	});

	GeoKBD.map('search-form', 'keyword');

	$('.full_desc, ul#searched').niceScroll({
		cursorborder: 'none',
		scrollspeed:  40,
		cursorcolor:  '#391a0a'
	});

	$('.purchase-btn').click(function() {
		var type = $(this).attr('id');
		var status = (type == 'price_four')
			? 'დარწმუნებული ხართ, რომ გსურთ ზღაპრის მოსმენა? ზღაპარი ხელმისაწვდომი იქნება 4 საათის განმავლობაში!'
			: 'დარწმუნებული ხართ, რომ გსურთ ზღაპრის შეძენა? ზღაპარი ხელმისაწვდომი იქნება ყოველთვის!';
		if (confirm(status) === true) {
			$('#buy-form button[type=button]').attr('disabled', 'disabled');
			$.post($('#buy-form').attr('action'), { buy: 1, type: type }, function(data) {
				if (data.res == 'success') {
					$('.player').html(data.html);
					$('audio').audioPlayer();
					hide_popup('buy');
					$('.audioplayer-playpause').trigger('click');
				} else {
					$('#buy-form button[type=button]').removeAttr('disabled');
					$('#buy').children('.status').text(data.txt).show('fast');
				}
			}, 'JSON');
			return false;
		}
	});

	$('.start-premium-btn').click(function() {
		var type = $(this).attr('data-Id');
		if (confirm('დარწმუნებული ხართ, რომ გსურთ პრემიუმ პაკეტის ჩართვა?') === true) {
			$('#premium-form button[type=button]').attr('disabled', 'disabled');
			$.post($('#premium-form').attr('action'), { premium: 1, type: type }, function(data) {
				if (data.res == 'success') {
					$('.player').html(data.html);
					$('audio').audioPlayer();
					hide_popup('premium');
					show_popup('confirm', 'პრემიუმ პაკეტი', data.txt);
				} else {
					$('#premium-form button[type=button]').removeAttr('disabled');
					$('#premium').children('.status').text(data.txt).show('fast');
				}
			}, 'JSON');
			return false;
		}
	});

	// Contact form with reCAPTCHA v3
	$('#contact-form').submit(function() {
		$('#contact-form input[type=submit]').attr('disabled', 'disabled');
		var form = this;
		grecaptcha.ready(function() {
			grecaptcha.execute('6LfOBJ0sAAAAABdctjr0j5vFv3up0pJoMw5vEZCz', { action: 'contact' }).then(function(token) {
				$('#recaptcha_token').val(token);
				$.post($('#contact-form').attr('action'), $(form).serialize(), function(data) {
					if (data.res == 'success') {
						$('#contact-form textarea, #contact-form .inputs input').val('');
						$('#contact-form input[type=submit]').removeAttr('disabled');
						hide_popup('registration', false, false);
						show_popup('confirm', 'კონტაქტი', data.txt);
					} else {
						$('#contact-form').children('.status').text(data.txt).show('fast');
						$('#contact-form input[type=submit]').removeAttr('disabled');
					}
				}, 'JSON');
			});
		});
		return false;
	});

	$('#balance-form').submit(function() {
		$('#balance-form input[type=submit]').attr('disabled', 'disabled');
		$.post($('#balance-form').attr('action'), $(this).serialize(), function(data) {
			if (data.res == 'success') {
				$('#balance-form input').attr('disabled', 'disabled');
				document.location.href = data.url;
				hide_popup('settings', false, false);
				show_popup('confirm', 'პარამეტრები', data.txt);
			} else {
				$('#balance-form').children('.status').text(data.txt).show('fast');
				$('#balance-form input[type=submit]').removeAttr('disabled');
			}
		}, 'JSON');
		return false;
	});

	$('#settings-form').submit(function() {
		$('#settings-form input[type=submit]').attr('disabled', 'disabled');
		$.post($('#settings-form').attr('action'), $(this).serialize(), function(data) {
			if (data.res == 'success') {
				$('#settings-form input').attr('disabled', 'disabled');
				hide_popup('settings', false, false);
				show_popup('confirm', 'პარამეტრები', data.txt);
			} else {
				$('#settings-form').children('.status').text(data.txt).show('fast');
				$('#settings-form input[type=submit]').removeAttr('disabled');
			}
		}, 'JSON');
		return false;
	});

	$('#registration-form').submit(function() {
		$('#registration-form input[type=submit]').attr('disabled', 'disabled');
		$.post($('#registration-form').attr('action'), $(this).serialize(), function(data) {
			if (data.res == 'success') {
				$('#registration-form input').attr('disabled', 'disabled');
				hide_popup('registration', false, false);
				show_popup('confirm', 'რეგისტრაცია', data.txt);
			} else {
				$('#registration-form').children('.status').text(data.txt).show('fast');
				$('#registration-form input[type=submit]').removeAttr('disabled');
			}
		}, 'JSON');
		return false;
	});

	$('#recovery-form').submit(function() {
		$('#recovery-form input[type=submit]').attr('disabled', 'disabled');
		$.post($('#recovery-form').attr('action'), $(this).serialize(), function(data) {
			if (data.res == 'success') {
				$('#recovery-form input').attr('disabled', 'disabled');
				hide_popup('recovery', false, false);
				show_popup('confirm', 'პაროლის აღდგენა', data.txt);
			} else {
				$('#recovery-form').children('.status').text(data.txt).show('fast');
				$('#recovery-form input[type=submit]').removeAttr('disabled');
			}
		}, 'JSON');
		return false;
	});

	$('#authorisation-form').submit(function() {
		$('#authorisation-form input[type=submit]').attr('disabled', 'disabled');
		$.post($('#authorisation-form').attr('action'), $(this).serialize(), function(data) {
			if (data.res == 'success') {
				$('#authorisation-form input').attr('disabled', 'disabled');
				location.href = '/';
			} else {
				$('#authorisation-form').parent('.authorisation').children('.status').text(data.txt).show('fast');
				$('#authorisation-form input[type=submit]').removeAttr('disabled');
			}
		}, 'JSON');
		return false;
	});

	$('audio').audioPlayer();

	$('.tooltip, label[for=remember]>span').tooltipster({
		theme:         'tooltipster-shadow',
		contentAsHTML: true
	});

	$('.premium_on').tooltipster({
		theme:         'tooltipster-shadow',
		position:      'right',
		contentAsHTML: true
	});

	$('#search-form').bind('keypress', function(e) {
		if (e.which == 13) return false;
	});

	$('#search').keyup(function() {
		var keyword = $(this).val();
		if (keyword.length < 4) {
			if ($('#search-result').is(':visible')) {
				$('#search-result').stop().slideUp('fast');
				$('ul#searched').html('<li><a href="javascript:void(0);">იძებნება...</a></li>');
			}
			return false;
		}
		$('ul#searched').html('<li><a href="javascript:void(0);">იძებნება...</a></li>');
		$('#search-result').stop().slideDown('fast');
		$.post($('search-form').attr('action'), { keyword: keyword }, function(data) {
			if (data.res == 'success') {
				$('ul#searched').html(data.html);
				$('#search-result').stop().slideDown('fast');
			} else {
				if ($('#search-result').is(':visible')) {
					$('ul#searched').html('<li><a href="javascript:void(0);">არაფერი მოიძებნა!</a></li>');
				}
			}
		}, 'JSON');
		return false;
	});

	$('.arrow').click(function(e) {
		e.preventDefault();
		if ($('.arrow').hasClass('disabled') == true) return false;
		var direction = $(this).attr('id');
		var type      = $(this).parent().attr('class');
		var page      = $(this).parent().children('.pages').children('span#from').text();
		var maxPage   = $(this).parent().children('.pages').children('span#to').text();
		var pg;
		if (direction == 'next') {
			pg = parseInt(page) + 1;
			if (pg > maxPage) return false;
		} else {
			pg = parseInt(page) - 1;
			if (pg < 1) return false;
		}
		$('.arrow').addClass('disabled');
		var url = (type == 'purchased') ? '/purchased' : '/tales';
		$.post(url, { page: pg, type: type }, function(data) {
			if (data.res == 'success') {
				$('ul.' + type).html(data.html);
				$('.' + type).children('.pages').children('span#from').text(pg);
				$('.arrow').removeClass('disabled');
				$('.tooltip').tooltipster({
					multiple:      true,
					theme:         'tooltipster-shadow',
					contentAsHTML: true
				});
				for (var i = 0; i < $('ul.' + type + ' li').length; i++) {
					var li = $('ul.' + type + ' li:eq(' + i + ')');
					showListing(li, i);
				}
			} else {
				$('.arrow').removeClass('disabled');
				alert(data.txt);
			}
		}, 'JSON');
		return false;
	});

	$('.audioplayer-playpause').click(function() {
		var Id = $(this).closest('.audioplayer').children('audio').attr('data-Id');
		$.post('/tale/' + Id, { tale_listen: 'listen' });
	});

	// Facebook SDK
	window.fbAsyncInit = function() {
		FB.init({
			appId:   '790277907718676',
			xfbml:   true,
			version: 'v2.2'
		});
	};
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) { return; }
		js = d.createElement(s);
		js.id  = id;
		js.src = '//connect.facebook.net/ka_GE/sdk.js';
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));

	// Google Analytics
	(function(i, s, o, g, r, a, m) {
		i['GoogleAnalyticsObject'] = r;
		i[r] = i[r] || function() { (i[r].q = i[r].q || []).push(arguments); };
		i[r].l = 1 * new Date();
		a = s.createElement(o);
		m = s.getElementsByTagName(o)[0];
		a.async = 1;
		a.src   = g;
		m.parentNode.insertBefore(a, m);
	})(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
	ga('create', 'UA-59989050-1', 'auto');
	ga('send', 'pageview');

});

$(document).click(function(e) {
	if (e.target.id == 'search') return false;
	if ($('#search-result').is(':visible')) {
		$('#search-result').stop().slideUp('fast');
	}
});
