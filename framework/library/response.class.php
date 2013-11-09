<?php
/**
 * XOMBO
 * response.class.php an object that represents the outbound response data
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
namespace XOMBO;
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
			if ($request->getClass () == "") {
				if (class_exists ("defaultController")) {
					$request->namespace = "";
					$request->class ="defaultController";
				} else {
					$request->namespace = "XOMBO";
					$request->class = "defaultController";
				}
			}
			if (!class_exists ($request->getClass ()) && class_exists ("XOMBO\\" . $request->getClass ())) {
				$request->namespace = "XOMBO";
			}
			$path = $request->getNamespace () != "" ? $request->getNamespace () . "\\" : "";
			$class = $request->getClass ();
			if (is_model ($path . $class) && !is_controller ($path . $class)) {
				$class .= "Controller";
				if (!class_exists ($class) && !class_exists ('XOMBO\\' . $class)) eval ("namespace XOMBO; class " . $class . " extends dbModelController { static function getModel () { return \"" . $request->getNamespace () . "\\\\" . $request->getClass () . "\"; } }");
			}
			switch (TRUE) {
				case !class_exists ($path . $class):
					throw new \exception ("Class does not exist", 404);
				case !is_controller ($path . $class):
					throw new \exception ("Class does not extend Controller", 404);
				case $path . $class != "" && array_key_exists ("method", $request->getFields ()) && !method_exists ($path . $class, $request->getMethod ()):
					throw new \exception ("Method does not exist", 404);
				break;
				case !array_key_exists ("method", $request->getFields ()):
					$request->addField ("method", "describe");
				case TRUE:
					if ($return) {
						$new = new response ($request);
					} else {
						$new = self::factory ($request);
					}
					$new->setResult (call_user_func_array ($path . $class . "::" . $request->getMethod (), count ($request->getParams ()) ? $request->getParams () : array (NULL)));
				break;
			}
		} catch (\exception $e) {
			if ($return) {
				$new = new response ($request);
			} else {
				$new = self::factory ($request);
			}
			$new->setError ($e->getCode (), $e->getMessage () . (array_key_exists ("debug", $_REQUEST) ? " " . $e->getFile () . "(" . $e->getLine () . ") " . $e->getTraceAsString () : ""));
		}
		return $new;
	}
	public static function &getAll () { return parent::getAll (__CLASS__); }
	public static function getCount () { return parent::getCount (__CLASS__); }

	public static function validateError ($obj, $name, $value) {
		if (is_array ($value) && array_key_exists ("code", $value) && is_numeric ($value["code"]) && array_key_exists ("message", $value) && is_string ($value["message"]))
			return $value;
		return NULL;
	}

	public static function validateResult ($obj, $name, $value) {
		if (is_array ($value) || is_string ($value) || is_bool ($value) || is_numeric ($value) || is_model ($value) || is_iterator ($value))
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
		$arg = func_get_arg (0);
		return self::store (new response ($arg));
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

	function encodeXML ($val, $namespace = NULL) {
		if ($val === TRUE || $val === FALSE) {
			return ($val === true ? "true" : "false");
		} else if (is_model ($val, FALSE)) {
			$path = explode ("\\", get_class ($val));
			$class = array_pop ($path);
			return "<" . $class . ">" . self::encodeXML ($val->getPublicFields (false), $class)  . "</" . $class . ">";
		} else if (is_iterator ($val, FALSE)) {
			return $val->valid () ? self::encodeXML ($val->current ()) . self::encodeXML ($val->next ()) : "";
		} else if (is_array ($val) && count ($val)) {
			$noKey = FALSE;
			if (is_numeric (array_key (array_keys ($val), 0))) {
				if (is_model (array_key (array_values ($val), 0))) {
					$noKey = TRUE;
				}
			}
			$return = "";
			foreach ($val as $key => $v) {
				$return .= ($noKey ? "" : "<" . (is_numeric ($key) ? "array id=\"$key\"" : $key) . ">") . self::encodeXML ($v) . ($noKey ? "" : "</" . (is_numeric ($key) ? "array" : $key) . ">");
			}
			return $return;
		} else if (!is_array ($val)) {
			return "<![CDATA[" . (string) $val . "]]>";
		}
		return "";
	}
	
	function encodeJSON ($val, $namespace = NULL) {
		if ($val === TRUE || $val === FALSE) {
			return ($val === TRUE ? "true" : "false");
		} else if (is_model ($val, FALSE)) {
			return self::encodeJSON ($val->getPublicFields (), get_class ($val));
		} else if (is_iterator ($val, FALSE)) {
			return ($namespace == NULL ? "[" : "") . ($val->valid () ? ($namespace == NULL ? "" : ",") . self::encodeJSON ($val->current ()) . self::encodeJSON ($val->next (), TRUE) : "]");
		} else if (is_array ($val) && count ($val)) {
			$noKey = FALSE;
			if (is_numeric (array_key (array_keys ($val), 0))) {
				if (is_model (array_key (array_values ($val), 0))) {
					$noKey = TRUE;
				}
			}
			$return = '';
			foreach ($val as $key => $v) {
				if (!empty ($return)) $return .= ',';
				$return .= ($noKey ? '' : '"' . addslashes($key) . '":' . self::encodeJSON ($v));
			}
			return '{' . $return . '}';
		} else if (!is_array ($val)) {
			return '"' . (string) addslashes ($val) . '"';
		}
		return '';
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
			return array_key_exists ("json", $_REQUEST) ? self::encodeJSON ($this, false) : self::encodeXML ($this) . "\n<!--<![CDATA[     See something? Say something! Help us improve support for your browser, here: http://rok.yt/10Z8N9     ]]>//-->";
		} catch (\exception $e) {
			ob_end_clean ();
			var_dump (debug_backtrace ());
			die ();
		}
	}
}

