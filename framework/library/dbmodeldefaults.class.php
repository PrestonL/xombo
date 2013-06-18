<?php
/**
 * XOMBO
 * dbmodeldefaults.class.php quite literally the default dbmodel object if you don't overload and extend it in your app/models folder
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