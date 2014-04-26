/**
 * XOMBO
 * site.js a work in progress to port over AJAX support from another application
 *
 * @license Apache License V2 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * @author Nolte Burke
 * 	
 * 	Copyright 2013 Nolte Burke
 * 	
 * 	Licensed under the Apache License, Version 2.0 (the "License");
 * 	you may not use this file except in compliance with the License.
 * 	You may obtain a copy of the License at
 * 	
 * 		http://www.apache.org/licenses/LICENSE-2.0
 * 	
 * 	Unless required by applicable law or agreed to in writing, software
 * 	distributed under the License is distributed on an "AS IS" BASIS,
 * 	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * 	See the License for the specific language governing permissions and
 * 	limitations under the License.
 */
var jsLoaded = [];
var fixIE = function () {
	// fixes issues w/ IE9
	$('input[type="submit"], input[type="button"], button').addClass ('button');
}
var userBackground = (function (obj) {
	var background = "";
	return function (obj) {
		if (typeof obj == 'undefined') obj = $("body");
		if (typeof obj.attr ("data-background") != 'undefined' && obj.attr ("data-background") != background) {
			$("body").css ("background", "#000000 url('" + obj.attr ("data-background") + "') top center");
			background = obj.attr ("data-background");
		}
	};
})();
var fixHover = function () {
	if (!navigator.userAgent.match(/(iPad|iPhone|iPod|BlackBerry|PlayBook|webOS|Android)/i)) {
		$('form input[type="button"], ' +
			'form input[type="submit"], ' +
			'form button.hover, ' +
			'#column #header div ul li.toggle a, ' +
			'#column #header div ul li a, ' +
			'#column #content ul li div, ' +
			'#column #content ul li.screenshot div').addClass ("hover");
	}
}
var contentResize = function () {
	if (typeof FB != 'undefined') FB.Canvas.setSize();
}
var screenshots = function () {
	$("li.screenshot").lazybackground ({
		data_attribute : "screenshot",
		appear : function () {
			$(this).find ("div:first").css ("background", "url(" + $(this).attr ("data-screenshot") + ") no-repeat").addClass ("screenshotLoaded");
		}
	});
}
var ajaxifyLinks = function () {
	$('a[rel="external"]').attr ("target", "_blank");
	if ($.xslt.transform () && !navigator.userAgent.match(/(Android|BlackBerry|PlayBook)/i)) {
		$('form[data-target], a[data-target]').each (function () {
			if (typeof $(this).attr ("data-href") == 'undefined' || !$(this).attr ("data-href").length) {
				$(this).attr ('data-href', $(this).is ("a") ? $(this).attr ("href") : $(this).attr ("action"));
				$(this).unbind ($(this).is ("form") ? "submit" : "click").bind ($(this).is ("form") ? "submit" : "click", function (e) {
					if ($(this).is ('form') && (typeof $(this).attr ("data-disable") == 'undefined' || $(this).attr ("data-disable") == "true")) {
						$(this).find ("input[type=\"submit\"]").attr ("disabled", "true");
					}
					ajaxLoad (
						$(this).is ("form[method=\"get\"]") ? $(this).attr ("data-href") + '?' + $(this).serialize () : $(this).attr ("data-href"),
						$(this).attr ("data-template"),
						$(this).attr ("data-target"),
						typeof $(this).attr ("data-targetFunc") == 'undefined' || !$(this).attr ("data-targetFunc").length ? null : $(this).attr ("data-targetFunc"),
						true,
						$(this).is ("form[method=\"post\"]") ? $(this).serialize () : null
					);
					e.stopPropagation ();
					if ($(this).is ('form') || $(this).attr ("href").substr (0, 1) != "#") {
						e.preventDefault ();
						return false;
					}
					return true;
				});
				if (typeof history == 'undefined' || typeof history.pushState == 'undefined') {
					$(this).attr (
						typeof $(this).attr ("action") == 'undefined' || $(this).attr ("action").length < 1 ? "href" : "action",
						'#{href:"' +
						$(this).attr ("data-href") +
						'",template:"' +
						$(this).attr ("data-template") +
						'",target:"' +
						$(this).attr ("data-target") +
						'"}'
					);
				}
			}
		});
	}
}
var ajaxLoad = function (xml, xsl, target, targetFunc, pushHistory, data) {
	if (typeof pushHistory == 'undefined') pushHistory = true;
	if (typeof data == 'undefined') data = null;
	$("body").css ("cursor", "progress");
	$("a, a *").css ("cursor", "wait");
	// push state to history
	if (pushHistory && typeof history != 'undefined' && typeof history.pushState != 'undefined') history.pushState ({
		href		:	xml,
		template	:	xsl,
		target		:	target
	}, $("title").text (), xml);
	return $.xslt ({
		xmlUrl : xml,
		xmlData : data,
		xslUrl : xsl,
		target : target,
		targetFunc : typeof targetFunc == 'undefined' ? null : targetFunc,
		callback : function (obj, options) {
			if (obj == false) return;
			obj.find ("noscript").remove ();
			document.title = obj.find ("title").text ();
			userBackground (obj.find ("body"));
			obj.find ('link[rel="stylesheet"]').each (function (i, el) {
				if (!$('link[rel="stylesheet"][href="' + $(el).attr ("href") + '"]').length) {
					if (typeof document.createStyleSheet != "undefined") {
						// for IE
					} else {
						$("head").append ($(el).clone ());
					}
				}
			});
			obj.find ("script").each (function (i, el) {
				if ($('script[src="' + $(el).attr ("src") + '"]').length == 0 && $.inArray ($(el).attr ("src"), jsLoaded) == -1) {
					jsLoaded.push ($(el).attr ("src"));
					$.getScript ($(el).attr ("src"));
				}
			});
			ajaxifyLinks ();
			screenshots ();
			fixIE ();
			fixHover ();
			$("body").css ("cursor", "default");
			$("a, a *").css ("cursor", "pointer");
			if (!navigator.userAgent.match(/(iPad|iPhone|iPod|BlackBerry|PlayBook|webOS|Android)/i)) {
				$(options.target).find ('[title]').tipTip ({ maxWidth: "150px", fadeIn: 0, fadeOut: 0 });
			}
			// report to google analytics
			_gaq.push(['_trackPageview', options.xmlUrl]);
			contentResize ();
			return;
		},
		xmlCache : false,
		xslCache : true
	});
}
var ajaxLoadState = function (state, pushHistory) {
	if (typeof pushHistory == 'undefined') pushHistory = false;
	if (typeof state == 'undefined' || state == null) return false;
	if (state.template != null && state.target != null && (document.location.href.indexOf ('#') ? document.location.href.substr (0, document.location.href.indexOf ('#')).indexOf (state.href) : document.location.href.indexOf (state.href)) < 0)
		return ajaxLoad (state.href, state.template, state.target, null, pushHistory);
	else document.location.href = state.href;
	return true;
}
var hashLoad = function () {
	// for browsers that don't support the history.pushState function, use hash + URL
	if (document.location.href.indexOf ('#') > -1 && document.location.href.substr (document.location.href.indexOf ('#') + 1).indexOf ('{') == 0) {
		eval ("data = " + document.location.href.substr (document.location.href.indexOf ('#') + 1) + ";");
		return ajaxLoadState (data);
	}
}
if (typeof history != 'undefined' && typeof history.pushState != 'undefined') $(window).bind ('popstate', function (e) {
	return ajaxLoadState (e.originalEvent.state);
});
// the following doesn't seem to be working with IE, to enable back/forward:
/*else $(window).bind ('hashchange', function (e) {
	hashLoad ();
	return true;
});*/
$(document).ready (function () {
	userBackground ();
	$("noscript").remove ();
	fixIE ();
	fixHover ();
	hashLoad ();
	if (typeof history != 'undefined' && typeof history.replaceState != 'undefined')
		history.replaceState ({ href : document.location.href, template : null, target : null }, $("title").text (), document.location.href);
	ajaxifyLinks ();
	$("hr").live ("click", function (event) {
		if (document.location.href.substr (0, 5) == "https") {
			document.location.href = "http://framework.xombo.com/";
		} else {
			ajaxLoad ("/", "/defaultcontroller/xsl", "#content, #header");
		}
	});
	$("#column #dialogue a.cancel[href=\"#\"]").live ("click", function (event) {
		$("#dialogue").css ("height", $("#dialogue").height ());
		$("#dialogue").children ().fadeOut (function () {
			$("#dialogue").slideUp (function () {
				$(this).empty ();
				$(this).css ("height", null);
			});
		});
		event.stopPropagation ();
		event.preventDefault ();
		return false;
	});
	if (!navigator.userAgent.match(/(iPad|iPhone|iPod|BlackBerry|PlayBook|webOS|Android)/i)) {
		$('[title]').tipTip ({ maxWidth: "150px", fadeIn: 0, fadeOut: 0 });
	}
	screenshots ();
});
var scrollLoad = true;
$(window).scroll (function (){
	if (!scrollLoad) return;
	scrollLoad = false;
	count = $("#column #content ul li.shortURL").length;
	if (count > 0 && $(window).scrollTop () == $(document).height () - $(window).height ()) {
		ajaxLoad ("/" + (document.location.href.indexOf ("user") > -1 ? "user/profile/" + $(".username").text () : "defaultcontroller/describe") + "/30/" + count, "/defaultcontroller/xsl", "#content", "append");
		setTimeout (function () {
			if ($("#column #content ul li.shortURL").length == count) {
				setTimeout (function () {
					scrollLoad = true;
				}, 30000);
			} else {
				scrollLoad = true;
			}
		}, 5000);
	} else {
		scrollLoad = true;
	}
});
