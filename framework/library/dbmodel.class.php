<?php
/**
 * XOMBO
 * dbmodel.class.php a class for auto-generating models from a database table description
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
abstract class dbModel extends factoryModel implements dbModelInterface {

	private $dbModelState;
	private $dontSaveChanges = FALSE;

	public static function init () { return; }

	private static function getChildTable () {
		return static::getTable ();
	}

	public static function validateBool ($obj, $name, $value) {
		return (bool) $value;
	}

	public static function validateInteger ($obj, $name, $value) {
		return (int) $value;
	}

	public static function validateString ($obj, $name, $value) {
		return (string) $value;
	}

	public static function select (array $params = array (), $order = array (), $limit = 0, $from = 0, $cache = false) {
		if (!is_array ($order) && is_numeric ($order)) { $cache = $from; $from = $limit; $limit = $order; $order = array (); } // allow skipping of ORDER BY parameter
		if (!is_numeric ($from) && is_bool ($from)) { $cache = $from; }
		else if (!is_numeric ($limit) && is_bool ($limit)) { $cache = $limit; }
		$table = self::getChildTable ();
		$query = "SELECT `ID` FROM `" . $table . "`";
		if (count ($params)) {
			$query .= " WHERE ";
			$first = TRUE;
			foreach ($params as $key => $value) {
				if ($first) $first = FALSE;
				else $query .= " AND ";
				$query .= "`" . $key . "`=\"" . DB::escape ($value) . "\"";
			}
		}
		$first = true;
		if (is_array ($order) && count ($order)) foreach ($order as $column => $direction) {
			if ($first) { $query .= " ORDER BY "; $first = false; } else { $query .= ", "; }
			$query .= "`" . $column . "`" . " " . (strtoupper ($direction) == "DESC" ? "DESC" : "ASC");
		}
		if ($limit > 0) {
			$query .= " LIMIT " . ($from > 0 ? $from . "," : "") . $limit;
		}
		$results = array ();
		$result = DB::query ($query, $cache);
		return new result (get_called_class (), $result);
	}

	public static function &getID ($ID = NULL) {
		$return = NULL;
		if (is_numeric ($ID) && $ID > 0)
			$return = static::select (array ("ID" => $ID), 1)->current ();
		if (!is_model ($return)) $return = NULL;
		return $return;
	}

	public function delete () {
		$ID = array_key_exists ("ID", $this->getFields ()) ? array_key ($this->getFields (), "ID") : NULL;
		if (is_numeric ($ID)) {
			DB::query ("DELETE FROM `" . self::getChildTable () . "` WHERE `ID`=\"" . DB::escape ($ID) . "\"");
			$this->dontSave ();
			return TRUE;
		}
		return FALSE;
	}

	public function __construct ($ID = NULL) {
		foreach (DB::describe (self::getChildTable ()) as $field => $type) {
			$this->addField (
				$field,
				array_key_exists ($field, DB::defaults (self::getChildTable ())) ? array_key (DB::defaults (self::getChildTable ()), $field) : NULL,
				method_exists (get_called_class (), "validate" . $field) ? "validate" . $field : (
					array_key_exists ($field, DB::describe (self::getChildTable ())) ?
						(method_exists (get_called_class (), "validate" . ucfirst (array_key (DB::describe (self::getChildTable ()), $field))) ? "validate" . ucfirst (array_key (DB::describe (self::getChildTable ()), $field)) : NULL) :
						NULL
				)
			);
		}
		if (is_numeric ($ID)) {
			$this->load ($ID);
		}
		$this->dbModelState = md5 (print_r ($this->getFields (FALSE), TRUE));
		return;
	}

	public function dontSave ($true = TRUE) {
		$this->dontSaveChanges = $true;
		return $this;
	}

	public function &load ($ID = NULL) {
		if (is_null ($ID)) $ID = $this->ID;
		if ($ID > 0) {
			$query = "SELECT";
			$first = TRUE;
			foreach ($this->getFields () as $key => $value) {
				if (array_key_exists ($key, DB::describe (self::getChildTable ()))) {
					if (!$first) $query .= ","; else $first = FALSE;
					$query .= " `" . $key . "`";
				}
			}
			$query .= " FROM `" . self::getChildTable () . "` WHERE `ID`=\"" . DB::escape ($ID) . "\" LIMIT 1";
			$row = DB::query ($query)->fetch_assoc ();
			if (is_array ($row)) foreach ($row as $field => $value) {
				$this->{$field} = $value;
			}
			if (static::isCacheable ()) {
				$this->cacheSet ();
			}
		}
		return $this;
	}

	public function &save () {
		if ($this->dontSaveChanges)
			return $this;
		if (md5 (print_r ($this->getFields (false), TRUE)) != $this->dbModelState) {
			$properties = $this->getFields (false);
			$query = "REPLACE INTO `" . self::getChildTable () . "` SET ";
			if (is_array ($properties)) {
				$first = TRUE;
				foreach ($properties as $name => $value) {
					if (!is_null ($value) && array_key_exists ($name, DB::describe (static::getChildTable ()))) {
						if ($first) $first = FALSE;
						else $query .= ", ";
						$query .= "`" . $name . "`=\"" . DB::escape ($value) . "\"";
					}
				}
			}
			error_log ("SQL: " . $query);
			DB::query ($query);
			if ($this->ID <= 0) {
				$this->ID = DB::lastID ();
			}
			// clear from memcache
			if (static::isCacheable ())
				$this->cacheDel ();
			$this->dbModelState = md5 (print_r ($this->getFields (false), TRUE));
		}
		return $this;
	}

	public function __destruct () {
		$this->save ();
	}

	protected static function cacheGet ($ID) {
		return DB::cacheGet (SITE_DOMAIN . ":" . get_called_class () . "(" . $ID . ")");
	}

	protected function cacheSet () {
		DB::cacheSet (SITE_DOMAIN . ":" . get_called_class () . "(" . $this->ID . ")", $this);
	}

	protected function cacheDel () {
		DB::cacheDel (SITE_DOMAIN . ":" . get_called_class () . "(" . $this->ID . ")", $this);
	}
}
