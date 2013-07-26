<?php
/**
 * XOMBO
 * request.class.php an object representing an incoming request to the application
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

	public function getNamespace () { return $this->namespace; }
	public function getClass () { return $this->class; }
	public function getMethod () { return $this->method; }
	public function setMethod ($method) { $this->method = $method; }
	public function getParams () { return $this->params; }

	public function __construct ($path = NULL) {
		$this->addField ("ID", self::getCount ());
		$args = is_string ($path) ? explode ('/', trim ($path, '/')) : array ();
		$this->addField ("namespace", "");
		if (count ($args)) $this->addField ("class", urldecode (array_shift ($args)));
		if (count ($args)) $this->addField ("method", urldecode (array_shift ($args)));
		$this->addField ("params", count ($args) ? $args : array (), "validateParams");
	}

	public function showAll () {
		return $this->getFields ();
	}
}