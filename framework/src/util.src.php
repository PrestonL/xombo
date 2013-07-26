<?php
/**
 * XOMBO
 * util.src.php various utility functions for dealing with new object types and data structures
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
function &array_key ($array, $key) {
	if (array_key_exists ($key, $array)) return $array[$key];
	$ret = NULL;
	return $ret;
}

function is_controller ($obj, $stringLiteralOK = TRUE) {
	return (is_object ($obj) || ($stringLiteralOK && is_string ($obj) && class_exists ($obj))) && array_key_exists ("XOMBO\controller", class_parents ($obj)) ? TRUE : FALSE;
}

function is_model ($obj, $stringLiteralOK = TRUE) {
	return (is_object ($obj) || ($stringLiteralOK && is_string ($obj) && class_exists ($obj))) && array_key_exists ("XOMBO\model", class_parents ($obj)) && !array_key_exists ("XOMBO\controller", class_parents ($obj)) ? TRUE : FALSE;
}

function is_iterator ($obj, $stringLiteralOK = TRUE) {
	return (is_object ($obj) || ($stringLiteralOK && is_string ($obj) && class_exists ($obj))) && array_key_exists ("Iterator", class_implements ($obj)) ? TRUE : FALSE;
}
