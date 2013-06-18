<?php
/**
 * XOMBO
 * factorymodel.class.php objects can be stored in a factory so that they persist in memory
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