<?php
/**
 * XOMBO
 * session.class.php basic user session stuff (not used?)
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
class session {
	private static $INSTANCE = NULL;
	private static $USER = NULL;
	public function __construct () {
		session_name (SESSION_COOKIE);
		session_set_cookie_params (SESSION_LIFETIME, SESSION_PATH, SESSION_DOMAIN, FALSE, FALSE);
		session_start ();
		return $this;
	}
	public function __destruct () {
		if (!is_null (self::$USER)) {
			self::$USER->commit ();
			$_SESSION['user'] = self::$USER;
		}
	}
	public static function init () {
		if (is_null (self::$INSTANCE)) {
			self::$INSTANCE = new session ();
		}
	}
	public function login ($user) {
		$_SESSION['user'] = $user;
		return true;
	}
	public function logout () {
		self::$USER = NULL;
		session_unset ();
		return true;
	}
	public static function verify () {
		if (strtotime (self::get ()->sessionExpires) > time ()) {
			return true;
		}
		return false;
	}
	public static function active () {
		return !is_null (self::get ()) && self::verify () ? true : false;
	}
	public static function &get () {
		if (is_null (self::$USER))
			self::$USER = array_key_exists ("user", $_SESSION) ? $_SESSION['user'] : NULL;
		return self::$USER;
	}
}