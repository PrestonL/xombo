<?php
class response extends factoryModel {
	const VERSION = "2.0";
	private $request;

	public static function shift () {
		return parent::shift ();
	}

	public static function &push ($result) {
		return self::factory (new request ())->setResult ($result);
	}

	public static function init () {
		while ($request = request::shift ()) {
			self::process ($request, FALSE);
		}
	}
	public static function &process ($request, $return = TRUE) {
		$new = NULL;
		try {
			$class = $request->getClass ();
			if (empty ($class)) {
				$class = "defaultController";
				$request->class = $class;
			}
			if (is_model ($class)) {
				$class = $class . "Controller";
				if (!class_exists ($class)) eval ("class " . $class . " extends dbModelController { protected static function getModel () { return \"" . $request->getClass () . "\"; } }");
			}
			switch (TRUE) {
				case !class_exists ($class):
					throw new exception ("Class does not exist");
				case !is_controller ($class):
					throw new exception ("Class does not extend Controller");
				case !empty ($class) && array_key_exists ("method", $request->getFields ()) && !method_exists ($class, $request->getMethod ()):
					throw new exception ("Method does not exist");
				break;
				case !array_key_exists ("method", $request->getFields ()):
					$request->addField ("method", "describe");
				case TRUE:
					if ($return) {
						$new = new response ($request);
					} else {
						$new = self::factory ($request);
					}
					$new->setResult (call_user_func_array ($class . "::" . $request->getMethod (), count ($request->getParams ()) ? $request->getParams () : array (NULL)));
				break;
			}
		} catch (exception $e) {
			if ($return) {
				$new = new response ($request);
			} else {
				$new = self::factory ($request);
			}
			$new->setError (500, $e->getMessage () . (array_key_exists ("debug", $_REQUEST) ? " " . $e->getFile () . "(" . $e->getLine () . ") " . $e->getTraceAsString () : ""));
		}
		return $new;
	}
	public static function getAll () { return parent::getAll (__CLASS__); }
	public static function getCount () { return parent::getCount (__CLASS__); }

	public static function validateError ($obj, $name, $value) {
		if (is_array ($value) && array_key_exists ("code", $value) && is_numeric ($value["code"]) && array_key_exists ("message", $value) && is_string ($value["message"]))
			return $value;
		return NULL;
	}

	public static function validateResult ($obj, $name, $value) {
		if (is_array ($value) || is_string ($value) || is_bool ($value) || is_numeric ($value) || is_model ($value))
			return $value;
		return NULL;
	}

	public function __construct (&$request) {
		$ID = $request->ID;
		$this->request = $request;
		$this->addField (array_key_exists ("json", $_REQUEST) ? "jsonrpc" : "xmlrpc", self::VERSION);
		$this->addField ("result", NULL, "validateResult");
		$this->addField ("error", array ("code" => 200, "message" => "OK"), "validateError");
		$this->addField ("id", $ID);
	}

	public static function &factory () {
		return self::store (new response (func_get_arg (0)));
	}

	public function &setError ($code, $message) {
		$this->error = array ("code" => $code, "message" => $message);
		return $this;
	}

	public function &setResult ($value) {
		$this->result = $value;
		return $this;
	}

	public function getRequest () {
		return $this->request;
	}

	function encodeXML (&$val, $namespace = NULL) {
		if ($val === true || $val === false) {
			return ($val === true ? "true" : "false");
		} else if (is_model ($val)) {
			return "<" . get_class ($val) . ">" . self::encodeXML ($val->getPublicFields (), get_class ($val))  . "</" . get_class ($val) . ">";
		} else if (is_array ($val) && count ($val)) {
			$noKey = FALSE;
			if (is_numeric (array_key (array_keys ($val), 0))) {
				if (is_model (array_key (array_values ($val), 0))) {
					$noKey = TRUE;
				}
			}
			$return = "";
			foreach ($val as $key => $v) {
				$return .= ($noKey ? "" : "<" . (is_numeric ($key) ? "string id=\"$key\"" : $key) . ">") . self::encodeXML ($v) . ($noKey ? "" : "</" . (is_numeric ($key) ? "string" : $key) . ">");
			}
			return $return;
		} else if (!is_array ($val)) {
			return "<![CDATA[" . (string) $val . "]]>";
		}
		return "";
	}

	function encodeJSON ($val, $namespace = NULL) {
		return json_encode (self::recurseJSON ($val));
	}

	function recurseJSON ($val) {
		if (is_model ($val)) {
			return self::recurseJSON ($val->getPublicFields ());
		} else if (is_array ($val) && count ($val)) {
			$return = array ();
			foreach ($val as $key => $val) {
				if (is_numeric ($key))
					$return[] = self::recurseJSON ($val);
				else $return[$key] = self::recurseJSON ($val);
			}
			return $return;
		}
		return $val;
	}

	public function __toString () {
		try {
			static $codes = array (
				200 => "OK",
				400 => "Bad Request",
				403 => "Forbidden",
				404 => "Not Found",
				500 => "Internal Server Error",
				501 => "Not Implemented",
				503 => "Service Unavailable"
			);
			if ($this->error["code"] == 200) {
				$this->delField ("error");
			} else {
				$this->delField ("result");
				header ("HTTP/1.1 " . $this->error["code"] . " " . (array_key_exists ($this->error["code"], $codes) ? $codes[$this->error["code"]] : "OK"));
			}
			return array_key_exists ("json", $_REQUEST) ? self::encodeJSON ($this) : self::encodeXML ($this) . "\n<!--<![CDATA[     See something? Say something! Help us improve support for your browser, here: http://rok.yt/10Z8N9     ]]>//-->";
		} catch (exception $e) {
			ob_end_clean ();
			var_dump (debug_backtrace ());
			die ();
		}
	}
}