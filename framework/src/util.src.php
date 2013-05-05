<?php
function &array_key (&$array, $key) {
	if (array_key_exists ($key, $array)) return $array[$key];
	$ret = NULL;
	return $ret;
}

function is_controller ($obj) {
	return (is_object ($obj) || (is_string ($obj) && class_exists ($obj))) && array_key_exists ("controller", class_parents ($obj)) ? TRUE : FALSE;
}

function is_model ($obj) {
	return (is_object ($obj) || (is_string ($obj) && class_exists ($obj))) && array_key_exists ("model", class_parents ($obj)) && !array_key_exists ("controller", class_parents ($obj)) ? TRUE : FALSE;
}

function is_iterator ($obj) {
	return (is_object ($obj) || (is_string ($obj) && class_exists ($obj))) && array_key_exists ("Iterator", class_implements ($obj)) ? TRUE : FALSE;
}
