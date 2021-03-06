<?php
/**
 * XOMBO
 * defaultcontroller.class.php quite literally the "default controller" if you don't override it in your app/controllers folder
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
class defaultController extends controller {
	public static function describe () {
		if (count (DB::allTables ()))
			return array_combine (DB::allTables (), array_pad (array (), count (DB::allTables ()), "class"));
		return array ();
	}
}
