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
			$("body").css ("cursor", "default");
			$("a, a *").css ("cursor", "pointer");
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
	$("noscript").remove ();
	hashLoad ();
	if (typeof history != 'undefined' && typeof history.replaceState != 'undefined')
		history.replaceState ({ href : document.location.href, template : null, target : null }, $("title").text (), document.location.href);
	ajaxifyLinks ();
});
