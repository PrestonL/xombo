<?php
abstract class factoryModel extends model {
	static $WAREHOUSE;

	abstract public static function init ();
	abstract public static function &factory ();

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
		if (!is_array (self::$WAREHOUSE)) self::$WAREHOUSE = array ();
		if (!array_key_exists (get_class ($obj), self::$WAREHOUSE)) self::$WAREHOUSE[get_class ($obj)] = array ();
		if (array_key_exists ("ID", $obj->getFields ()) || array_key_exists ("id", $obj->getFields ())) self::$WAREHOUSE[get_class ($obj)][array_key ($obj->getFields (), array_key_exists ("ID", $obj->getFields ()) ? "ID" : "id")] = $obj;
		else self::$WAREHOUSE[get_class ($obj)][] = $obj;
		return self::$WAREHOUSE[get_class ($obj)][array_key_exists ("ID", $obj->getFields ()) ? array_key ($obj->getFields (), "ID") : count (self::$WAREHOUSE[get_class ($obj)]) - 1];
	}

	protected static function shift () {
		if (array_key_exists (get_called_class (), self::$WAREHOUSE) && count (self::$WAREHOUSE[get_called_class ()]))
			return array_shift (self::$WAREHOUSE[get_called_class ()]);
		return NULL;
	}
}