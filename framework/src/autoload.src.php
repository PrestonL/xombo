<?php
/**
 * XOMBO
 * autoload.src.php SPL function to auto-load classes upon first reference
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
chdir (__DIR__ . "/../..");
require_once ("./config.php");
require_once ("./framework/src/util.src.php");
spl_autoload_register (function ($class) {
	$orig = $class;
	$path = array ();
	if (strpos ($class, "\\")) {
		$path = explode ("\\", $class);
		$class = array_pop ($path);
	}
	if (count ($path) && $path[0] == "XOMBO" && file_exists ("./framework/library/" . strtolower ($class) . ".class.php")) {
		require_once ("./framework/library/" . strtolower ($class) . ".class.php");
	} else if ((!count ($path) || $path[0] != "XOMBO") && file_exists ("./app/classes/" . strtolower ($class) . ".class.php")) {
		require_once ("./app/classes/" . strtolower ($class) . ".class.php");
	} else if ((!count ($path) || $path[0] != "XOMBO") && file_exists ("./app/controllers/" . strtolower ($class) . ".class.php")) {
		require_once ("./app/controllers/" . strtolower ($class) . ".class.php");
	} else if ((!count ($path) || $path[0] != "XOMBO") && file_exists ("./app/models/" . strtolower ($class) . ".class.php")) {
		require_once ("./app/models/" . strtolower ($class) . ".class.php");
	} else if (XOMBO\DB::exists (strtolower ($class))) {
		eval ("namespace XOMBO; class " . $class . " extends dbModelDefaults { }");
	} else if (strpos (strtolower ($class), "virtual") === 0) {
		eval ("class " . $class . " extends XOMBO\\factoryModel {" . '
			public static function init () { return; }
			public static function &factory () {
				$return = self::store (new ' . $class . ' ());
				return $return;
			}
			public function &addField ($name, $default = NULL, $validator = NULL) {
				return parent::addField ($name, $default, $validator);
			}
		}');
	}
	if (class_exists ($orig, FALSE)) {
		$reflector = new reflectionClass ($orig);
		if (method_exists ($orig, "init") && !$reflector->isAbstract ()) call_user_func ($orig . "::init");
		return true;
	}
	return false;
});