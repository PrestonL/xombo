<?php
abstract class factoryModel extends model implements factoryModelInterface {
	static $WAREHOUSE;

	protected static function getCount () {
		if (!is_array (self::$WAREHOUSE)) return 0;
		return array_key_exists (get_called_class (), self::$WAREHOUSE) ? count (self::$WAREHOUSE[get_called_class ()]) : 0;
	}

	protected static function &getAll () {
		if (!is_array (self::$WAREHOUSE)) self::$WAREHOUSE = array ();
		if (array_key_exists (get_called_class (), self::$WAREHOUSE))
			return self::$WAREHOUSE[get_called_class ()];
		$blank = array ();
		return $blank;
	}

	protected static function &store (&$obj) {
		$class = get_class ($obj);
		$id = array_key_exists ("ID", $obj->getFields ()) ? array_key ($obj->getFields (), "ID") : array_key_exists ("id", $obj->getFields ()) ? array_key ($obj->getFields (), "id") : NULL;
		if (!is_array (self::$WAREHOUSE)) self::$WAREHOUSE = array ();
		if (!array_key_exists ($class, self::$WAREHOUSE)) self::$WAREHOUSE[$class] = array ();
		if (!is_null ($id)) {
			self::$WAREHOUSE[$class][$id] = $obj;
		} else {
			self::$WAREHOUSE[$class][] = $obj;
		}
		return self::$WAREHOUSE[$class][!is_null ($id) ? $id : count (self::$WAREHOUSE[$class]) - 1];
	}

	protected static function shift () {
		if (array_key_exists (get_called_class (), self::$WAREHOUSE) && count (self::$WAREHOUSE[get_called_class ()]))
			return array_shift (self::$WAREHOUSE[get_called_class ()]);
		return NULL;
	}
}