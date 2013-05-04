<?php
abstract class dbModelDefaults extends dbModel {

	public static function getTable () {
		return strtolower (get_called_class ());
	}

	public static function isCacheable () {
		return false;
	}

	public static function &factory () { // $ID
		$ID = func_num_args () ? func_get_arg (0) : NULL;
		$class = get_called_class ();
		if (is_numeric ($ID) && $ID > 0) {
			if (static::isCacheable ()) {
				$obj = static::cacheGet ($ID);
				if ($obj !== false)
					return self::store ($obj);
			}
			if (is_array (self::$WAREHOUSE) && array_key_exists ($class, self::$WAREHOUSE) && array_key_exists ($ID, self::$WAREHOUSE[$class]))
				return self::$WAREHOUSE[$class][$ID];
		}
		return self::store (new $class ($ID));
	}
}