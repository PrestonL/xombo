<?php
use XOMBO\ul_li as ul_li;
class ul extends uicomponent {
	public $items = array ();
	function __construct ($ID = NULL, $section_component_ID = NULL) {
		parent::__construct ($ID, $section_component_ID);
		if ($this->ID > 0) {
			$this->items = ul_li::select (array ('ul_ID' => $this->ID), array ('order' => 'ASC'))->bind (
				function ($obj) {
					return new li ($obj->li_ID, $obj->ID);
				}
			)->getArray ();
		}
	}
	public static function getTable () {
		return "ul";
	}
	public function createLi () {
		$li = new li ();
		$li->content = "new li";
		$li->save ();
		if ($li->ID > 0) {
			$ul_li = new ul_li ();
			$ul_li->ul_ID = $this->ID;
			$ul_li->li_ID = $li->ID;
			$ul_li->order = count ($this->items) + 1;
			$ul_li->save ();
			if ($ul_li->ID > 0) {
				return $li;
			}
			$li->delete ();
			throw new exception ("Could not add li to ul.");
		}
		throw new exception ("Could not create li.");
	}
}
