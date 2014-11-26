<?php
class siteController extends XOMBO\dbModelController {
	public static function getModel () {
		return "site";
	}
	public static function describe () {
		return static::all ();
	}
	public static function all ($limit = 5, $from = 0) {
		return static::view (1)->allPosts ($limit, $from);
	}
	public static function category ($category, $limit = 5, $from = 0) {
		return static::view (1)->postsByCategory ($category, $limit, $from);
	}
	public static function post ($ID) {	
		return static::view (1)->postById ($ID);
	}
}
