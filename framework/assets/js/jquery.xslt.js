/*

	XSLT jQuery Plugin - Copyright (C) 2007, Jorrit Jongma (Chainfire)
	XSLT jQuery Plugin - Copyright (C) 2012, Nolte Burke

	Version: 0.8
	Homepage: http://www.jongma.org/webtools/jquery/
	Homepage: http://xombo.com/

	License: MIT / Public Domain (whatever works for you)

	This is a jQuery Plugin for XSLT. It gives you the
	ability to transform XML/XSL from jQuery using the
	browser's built-in XSL transformation engine. This
	version differs from that of Jorrit Jongma's in
	that it no longer uses the Google AJAXSLT codebase.

*/
(function ($) {
	// $.xslt(options) - Return transformed XML
	$.xslt = function (options) {
		// Default settings
		var opt = {
			xml			:	null,
			xmlUrl		:	null,
			xmlData		: 	null,
			xmlCache	:	true,
			xsl 		:	null,
			xslUrl		:	null,
			xslCache	:	true,
			callback	:	null,
			target		:	null,
			targetFunc	:	null,
			error		:	false
		};
		$.extend (opt, options);

		// Can we go async?
		opt.async = ((opt.callback != null) || (opt.target != null));
		if (opt.targetFunc == null)
			opt.targetFunc = 'replaceWith';
		// Setup finish function
		opt.finish = function (opt) {
			if ((opt.xml != null) && (opt.xsl != null) && (opt.error == false)) {
				// We got the data and no error occured

				// Perform the transform
				var response = $.xslt.transform (opt.xml, opt.xsl);

				// Set target content to transformed XML
				if (opt.target != null) {
					targets = opt.target.replace (/[-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|\#]/g, "\\$&").split (/\s*,\s+/);
					targetFuncs = opt.targetFunc.replace (/[-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|\#]/g, "\\$&").split (/\s*,\s+/);
					lastTargetFunc = targetFuncs[targetFuncs.length - 1];
					while (targetFuncs.length < targets.length)
						targetFuncs.push (lastTargetFunc);
					evals = [];
					$.each (targets, function (i, target) {
							evals.push ('$("' + $.trim (target) + '").' + $.trim (targetFuncs[i]) + ' (response.find ("' + $.trim (target) + '")' + ($.trim (targetFuncs[i]).indexOf ("pend") > -1 ? '.contents ()' : '') + '.clone ()); $("' + $.trim (target) + ':hidden").show ();');
					});
					eval (evals.join ("\n"));
				}

				// Callback
				if (opt.callback != null) {
					opt.callback (response, opt);
				}

				// Return output for sync calls
				return response;
			} else if (opt.error) {
				// Error occured
				if (opt.callback != null) {
					opt.callback (false, opt);
				}
				return false;
			} else {
				// Data not in yet
				return true;
			}
		}

		if (((opt.xml == null) && (opt.xmlUrl == null)) || ((opt.xsl == null) && (opt.xslUrl == null))) {
			// Not going to work.
			opt.error = true;
			return opt.finish (opt);
		}

		// Retrieve XML and XSL from cache if possible
		if ((opt.xml == null) && (opt.xmlUrl != null) && (opt.xmlCache == true) && ($.xslt.cache.xml[opt.xmlUrl])) {
			opt.xml = $.xslt.cache.xml[opt.xmlUrl];
		}

		if ((opt.xsl == null) && (opt.xslUrl != null) && (opt.xslCache == true) && ($.xslt.cache.xsl[opt.xslUrl])) {
			opt.xsl = $.xslt.cache.xsl[opt.xslUrl];
		}

		// Get XML and XSL from url if necessary
		if ((opt.xmlUrl != null) && (opt.xml == null)) {
			$.ajax ({
				url			:	(opt.xmlUrl.indexOf ('?') > -1 ? opt.xmlUrl + "&xml" : opt.xmlUrl + "?xml"),
				data		:	(opt.xmlData !== null ? opt.xmlData : null),
				dataType	:	"xml",
				type		:	(opt.xmlData !== null ? "POST" : "GET"),
				error		:	function (err) {
									opt.xml = err.responseXML;
									opt.finish(opt);
								},
				success		:	function (data) {
									opt.xml = data;
									if (opt.xmlCache) {
										$.xslt.cache.xml[opt.xmlUrl] = data;
									}
									opt.finish(opt);
								},
				async		:	opt.async
			});
		}

		if ((opt.xslUrl != null) && (opt.xsl == null)) {
			$.ajax ({
				url: opt.xslUrl,
				dataType: 'xml',
				error		:	function () {
									opt.error = true;
									opt.finish (opt);
								},
				success		:	function (data) {
									opt.xsl = data;
									if (opt.xslCache) {
										$.xslt.cache.xsl[opt.xslUrl] = data;
									}
									opt.finish (opt);
								},
				async		:	opt.async
			});
		}

		// Return the transformed XML (string) if we're done, true if
		// we're working async, false if there has been an error
		return opt.finish (opt);
	}

	$.extend ($.xslt, {
		// $.xslt.version - Plugin version
		version		:	0.70,

		// XML / XSL cache
		cache		:	{
							xml: {},
							xsl: {}
						},

		transform	:	function (xml, xsl) {
							if (window.ActiveXObject) {
								// code for IE
								if (typeof xml != 'undefined' && typeof xsl != 'undefined')
									return ($(xml.transformNode (xsl)));
								return true;
							} else if (document.implementation && document.implementation.createDocument) {
								// other browsers
								if (typeof xml != 'undefined' && typeof xsl != 'undefined') {
									xsltProcessor = new XSLTProcessor ();
									xsltProcessor.importStylesheet (xsl);
									return $(xsltProcessor.transformToDocument (xml, document));
								}
								return true;
							}
							return false;
						}
	});

	// $(...).xslt(options) - Set content to transformed XML
	// xml and xsl parameters should be DOM nodes (not text)
	$.fn.xslt = function (options) {
		options.target = this;
		$.xslt (options);
		return this;
	}
})(jQuery);