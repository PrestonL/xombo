<?php
class siteController extends XOMBO\dbModelController {
	public static function getModel () {
		return "site";
	}
	public static function describe () {
		return static::search ();
	}
}
