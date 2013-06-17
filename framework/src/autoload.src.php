<?php
chdir (__DIR__ . "/../..");
require_once ("./config.php");
require_once ("./framework/src/util.src.php");
spl_autoload_register (function ($class) {
	if (file_exists ("./app/classes/" . strtolower ($class) . ".class.php")) {
		require_once ("./app/classes/" . strtolower ($class) . ".class.php");
	} else if (file_exists ("./app/controllers/" . strtolower ($class) . ".class.php")) {
		require_once ("./app/controllers/" . strtolower ($class) . ".class.php");
	} else if (file_exists ("./app/models/" . strtolower ($class) . ".class.php")) {
		require_once ("./app/models/" . strtolower ($class) . ".class.php");
	} else if (file_exists ("./framework/library/" . strtolower ($class) . ".class.php")) {
		require_once ("./framework/library/" . strtolower ($class) . ".class.php");
	} else if (DB::exists (strtolower ($class))) {
		eval ("class " . $class . " extends dbModelDefaults { }");
	} else if (strpos (strtolower ($class), "virtual") === 0) {
		eval ('class ' . $class . ' extends factoryModel {
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
	if (class_exists ($class, FALSE)) {
		$reflector = new reflectionClass ($class);
		if (method_exists ($class, "init") && !$reflector->isAbstract ()) call_user_func ($class . "::init");
		return true;
	}
	return false;
});