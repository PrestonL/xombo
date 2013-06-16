<?php
class request extends factoryModel {

	public static function &getAll () { return parent::getAll (__CLASS__); }
	public static function getCount () { return parent::getCount (__CLASS__); }

	public static function init () {
		$requests = explode ("|", trim (parse_url (str_replace ("%7C", "|", $_SERVER['REQUEST_URI']), PHP_URL_PATH), "|"));
		foreach ($requests as $request) {
			self::factory ($request);
		}
	}

	public static function validateParams ($obj, $name, $value) {
		if (is_array ($value)) {
			$return = array ();
			foreach ($value as $key => $val) {
				$return[$key] = urldecode ($val);
			}
			return $return;
		}
		return array ();
	}

	public static function &factory () { // $path
		return self::store (new request (func_get_arg (0)));
	}


	public function getClass () { return $this->class; }
	public function getMethod () { return $this->method; }
	public function setMethod ($method) { $this->method = $method; }
	public function getParams () { return $this->params; }

	public function __construct ($path = NULL) {
		$this->addField ("ID", self::getCount ());
		$args = is_string ($path) ? explode ('/', trim ($path, '/')) : array ();
		if (count ($args)) $this->addField ("class", urldecode (array_shift ($args)));
		if (count ($args)) $this->addField ("method", urldecode (array_shift ($args)));
		$this->addField ("params", count ($args) ? $args : array (), "validateParams");
	}

	public function showAll () {
		return $this->getFields ();
	}
}