<?php
abstract class controller extends model {
	private static $xsl = "";
	public static function describe () {
		$methods = get_class_methods (get_called_class ());
		$return = array ();
		foreach ($methods as $method) {
			if (strpos ($method, "__") !== 0) {
				$reflection = new reflectionMethod (get_called_class (), $method);
				if ($reflection->isPublic ())
					$return[$method] = "method";
			}
		}
		return $return;
	}

	public static function xsl ($return = false) {
		$class = method_exists (get_called_class (), "getModel") ? static::getModel () : get_called_class ();
		if (!$return) header ("Content-Type: text/xml");

		$file = "./app/assets/xsl/" . strtolower (get_called_class ()) . ".xsl";
		if (!file_exists ($file))
			$file = "./app/assets/xsl/" . str_replace ("controller", "", strtolower (get_called_class ())) . ".xsl";
		if (!file_exists ($file))
			$file = "./app/assets/xsl/default.xsl";
		if ($return)
			ob_start ();
		self::$xsl = file_get_contents ($file);
		echo '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
		self::$xsl = preg_replace_callback (
						'/(\<xsl:include href=\")([a-zA-Z0-9\.\/\-\_]+)(\")([\s]*)(\/\>)/',
						"self::__xslInclude",
						self::$xsl
					);
		// once webkit fixes their bug this won't be necessary anymore:
		if (!(strpos ($_SERVER['HTTP_USER_AGENT'], "WebKit/") === false) || array_key_exists ("html", $_REQUEST)) {
			self::$xsl = preg_replace_callback (
							'/(\<xsl:value\-of select=\"\$)([a-zA-Z0-9\.\-\_]+)([a-zA-Z0-9\.\/\-\_]*)(\")([\s]*)(\/\>)/',
							"self::__xslValueOfVariable",
							self::$xsl
						);
			// remove variable document tag
			self::$xsl = preg_replace ('/(\<xsl:variable name=\")([a-zA-Z0-9\.\-\_]+)(\")([\s]+)(select=\")(document\(\\\')([a-zA-Z0-9\.\-\_\/]+)(\\\'\))([a-zA-Z0-9\.\-\_\/]+)(\")([\s]*)(\/\>)/', "", self::$xsl);
		}
		echo self::$xsl;
		if ($return) return ob_get_clean ();
		exit (0);
	}
	private static function __xslInclude ($matches) {
		return file_get_contents ($_SERVER["DOCUMENT_ROOT"] . "/app/assets" . $matches[2]);
	}
	private static function __xslValueOfVariable ($matches) {
		static $varXML = array ();
		static $varXPath = array ();
		static $varPath = array ();
		try {
			if (!array_key_exists ($matches[2], $varXML) || is_null ($varXML[$matches[2]])) {
				// find the variable
				$var = array ();
				$expr = '/(\<xsl:variable name=\"' . $matches[2] . '\")([\s]+)(select=\")(document\(\\\')([a-zA-Z0-9\.\-\_\/]+)(\\\'\))([a-zA-Z0-9\.\-\_\/]+)(\")([\s]*)(\/\>)/';
				if (preg_match ($expr, self::$xsl, $var)) {
					$varXML[$matches[2]] = new DOMDocument ();
					if (preg_match ("/\.xml$/", $var[5])) {
						if (!$varXML[$matches[2]]->load ($_SERVER["DOCUMENT_ROOT"] . $var[5])) {
							unset ($varXML[$matches[2]]);
						}
					} else {
						$xml = response::process (new Request ($var[5]));
						$varXML[$matches[2]]->loadXML ((string) $xml);
					}
					if (array_key_exists ($matches[2], $varXML))
						$varXPath[$matches[2]] = new DOMXPath ($varXML[$matches[2]]);
					$varPath[$matches[2]] = $var[7];
				}
			}
			if (array_key_exists ($matches[2], $varXPath)) {
				$ret = $varXPath[$matches[2]]->evaluate ($varPath[$matches[2]] . $matches[3]);
				if ($ret->length)
					return ($ret->item (0)->nodeValue);
			}
		} catch (exception $e) {
			return "";
		}
	}
}
