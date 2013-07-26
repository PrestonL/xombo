<?php
/**
 * XOMBO
 * dbmodelcontroller.class.php a type of default controller specifically for dealing with dbmodels
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
abstract class dbModelController extends controller implements dbModelControllerInterface {

	private static function getModelByID ($ID) {
		$obj = call_user_func (static::getModel () . "::getID", $ID);
		if (!is_model ($obj))
			throw new \exception ("The selected ID could not be found.");
		return $obj;
	}

	public static function view ($ID) {
		return self::getModelByID ($ID);
	}

	public static function search () {
		$query = array ();
		$obj = call_user_func (static::getModel () . "::factory");
		if (func_num_args () % 2 == 0) {
			for ($i = 0; $i < func_num_args (); $i = $i + 2) {
				if (array_key_exists (func_get_arg ($i), $obj->getFields ()))
					$query[func_get_arg ($i)] = func_get_arg ($i + 1);
				else
					throw new \exception ("Property " . func_get_arg ($i) . " does not exist");
			}
		} else if (func_get_arg (0) !== NULL) {
			throw new \exception ("Mismatched key/value pairs");
		}
		return call_user_func (static::getModel () . "::select", $query);
	}

	public static function update ($ID) {
		$args = func_get_args ();
		$obj = $ID > 0 ? self::getModelByID ($ID) : call_user_func (static::getModel () . "::factory");
		if (count ($args) < 2) {
			foreach ($_REQUEST as $key => $val) {
				if (array_key_exists ($key, $obj->getFields ())) {
					$args[] = $key;
					$args[] = $val;
				}
			}
		}
		if (count ($args) % 2 == 1) for ($i = 1; $i < count ($args); $i = $i + 2) {
			if (array_key_exists ($args[$i], $obj->getFields ()))
				$obj->{$args[$i]} = $args[$i + 1];
			else
				throw new \exception ("Property " . $args[$i] . " does not exist");
		} else {
			throw new \exception ("Mismatched key/value pairs");
		}
		return $obj->save ();
	}

	public static function delete ($ID) {
		return self::getModelByID ($ID)->delete ();
	}

	public static function describe () {
		eval ("\$model = " . static::getModel () . "::factory ();");
		$inFields = DB::describe (call_user_func (static::getModel () . "::getTable"));
		$fields = array ();
		foreach ($inFields as $field => $type) $fields["__" . $field] = $type;
		return array_merge (parent::describe (), $fields);
	}
}
