<?php
class defaultController extends controller {
	public static function describe () {
		return array_combine (DB::allTables (), array_pad (array (), count (DB::allTables ()), "class"));
	}
}