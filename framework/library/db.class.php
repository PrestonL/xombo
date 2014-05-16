<?php
/**
 * XOMBO
 * db.class.php database connection management class
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
class db {
	const SERVER	=	DB_HOSTNAME;
	const USER		=	DB_USERNAME;
	const PASS		=	DB_PASSWORD;
	const SCHEMA 	=	DB_SCHEMA;

	const BOOLEAN	=	"bool";
	const INTEGER	=	"integer";
	const FLOAT		=	"float";
	const STRING	=	"string";

	static $CACHE_LENGTH = 60;
	static $CACHE_LENGTH_LONG = 300;
	static $CACHE_CHANGED = false;

	static $INSTANCE; // kicks off the destructor to save the cache
	static $CONNECTION;
	static $MEMCACHE;
	static $TABLES;
	static $DEFAULTS;
	static $COMMENTS;

	public static function init () {
		self::$INSTANCE = new db ();
		$cache = self::cacheGet ("DB_CACHE");
		if ($cache !== false) {
			if (is_array ($cache["TABLES"]))
				self::$TABLES = $cache["TABLES"];
			if (is_array ($cache["DEFAULTS"]))
				self::$DEFAULTS = $cache["DEFAULTS"];
			if (is_array ($cache["COMMENTS"]))
				self::$COMMENTS = $cache["COMMENTS"];
		} else {
			self::$TABLES = array ();
			self::$DEFAULTS = array ();
			self::$COMMENTS = array ();
		}
	}

	public function __destruct () {
		if (self::$CACHE_CHANGED) {
			$cache = array (
				"TABLES" => self::$TABLES,
				"DEFAULTS" => self::$DEFAULTS,
				"COMMENTS" => self::$COMMENTS
			);
			self::cacheSet ("DB_CACHE", $cache);
		}
	}

	private static function &conn () {
		if (is_null (self::$CONNECTION) || !self::$CONNECTION->ping ()) {
			self::$CONNECTION = new \mySQLi (self::SERVER, self::USER, self::PASS, self::SCHEMA);
			self::query ("SET NAMES 'utf8'");
		}
		return self::$CONNECTION;
	}

	private static function &mem () {
		if (MEMCACHE_ENABLED) {
			if (is_null (self::$MEMCACHE)) {
				self::$MEMCACHE = new \memcache ();
				self::$MEMCACHE->addServer (MEMCACHE_HOSTNAME, 11211);
			}
			return self::$MEMCACHE;
		}
		return NULL;
	}

	static function cacheSet ($key, $obj) {
		if (MEMCACHE_ENABLED) {
			if (!self::mem ()->replace ($key, $obj, MEMCACHE_COMPRESSED, self::$CACHE_LENGTH_LONG)) {
				self::mem ()->set ($key, $obj, MEMCACHE_COMPRESSED, self::$CACHE_LENGTH_LONG);
			}
		}
		return $obj;
	}

	static function cacheGet ($key) {
		if (MEMCACHE_ENABLED) return self::mem ()->get ($key);
		return false;
	}

	static function cacheDel ($key) {
		if (MEMCACHE_ENABLED) return self::mem ()->delete ($key);
		return true;
	}

	static function &query ($query) {
		$db_result = self::conn ()->query ($query);
		return $db_result;
	}

	static function escape ($value) {
		return self::conn ()->real_escape_string ($value);
	}

	static function lastID () {
		return mysqli_insert_id (self::conn ());
	}

	private static function cacheTable ($table) {
		if (!array_key_exists ($table, self::$TABLES)) {
			self::$TABLES[$table] = array ();
			self::$DEFAULTS[$table] = array ();
			self::$COMMENTS[$table] = array ();
			$result = self::query ("SHOW FULL COLUMNS IN `" . self::escape ($table) . "`", true);
			while ($row = $result->fetch_assoc ()) {
				switch (!FALSE) {
					case (strpos (" " . $row['Type'], "bit")):
						$type = self::BOOLEAN;
						break;
					case (strpos (" " . $row['Type'], "decimal")):
					case (strpos (" " . $row['Type'], "int")):
					case (strpos (" " . $row['Type'], "bigint")):
					case (strpos (" " . $row['Type'], "tinyint")):
						$type = self::INTEGER;
						break;
					case (strpos (" " . $row['Type'], "float")):
						$type = self::FLOAT;
						break;
					default:
						$type = self::STRING;
						break;
				}
				self::$TABLES[$table][$row['Field']] = $type;
				self::$DEFAULTS[$table][$row['Field']] = $row['Default'];
				self::$COMMENTS[$table][$row['Field']] = $row['Comment'];
			}
			self::$CACHE_CHANGED = true;
		}
	}

	public static function &describe ($table) {
		self::cacheTable ($table);
		return self::$TABLES[$table];
	}

	public static function &defaults ($table) {
		self::cacheTable ($table);
		return self::$DEFAULTS[$table];
	}

	public static function &comments ($table) {
		self::cacheTable ($table);
		return self::$COMMENTS[$table];
	}

	public static function exists ($table) {
		if (!is_string ($table)) return FALSE;
		return array_search ($table, self::allTables ()) === FALSE ? FALSE : TRUE;
	}

	public static function allTables () {
		static $tables = array ();
		if (count ($tables) == 0) {
			$result = DB::query ("SHOW TABLES", true);
			while ($row = $result->fetch_assoc ()) {
				$tables[] = array_shift ($row);
			}
		}
		return $tables;
	}
}
