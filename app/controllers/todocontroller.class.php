<?php
class todoController extends XOMBO\dbModelController {
	public static function getTodoText ($array = NULL) {
		static $todo_text = array ();
		if (!is_null ($array)) $todo_text = $array;
		return $todo_text;
	}
	public static function getModel () {
		return "todo";
	}
	public static function describe () {
		return static::search ();
	}
	public static function complete ($todo_ID, $complete = 1) {
		$todo = new todo ($todo_ID);
		$todo->completed = $complete;
		unset ($todo);
		return todo::select ();
	}
	public static function saveall () {
		$todo_new = array_key_exists ("todo_new", $_REQUEST) ? $_REQUEST['todo_new'] : "";
		$todo_text = array_key_exists ("todo_text", $_REQUEST) ? $_REQUEST['todo_text'] : array ();
		if (strlen ($todo_new)) {
			$todo = new todo ();
			$todo->title = $todo_new;
			unset ($todo);
		}
		if (is_array ($todo_text)) static::getTodoText ($todo_text);
		$todos = todo::select ()->bind (function ($obj) {
			if (array_key_exists ($obj->ID, todoController::getTodoText ())) {
				if (array_key (todoController::getTodoText (), $obj->ID) == "") {
					$obj->delete ();
					return NULL;
				}
				$obj->title = array_key (todoController::getTodoText (), $obj->ID);
			}
			return $obj;
		});;
		return $todos;
	}
}
