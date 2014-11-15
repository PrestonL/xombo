<?php
use XOMBO\section_component as section_component;
use XOMBO\component as component;
class section extends XOMBO\dbModelDefaults {
	public $components = array ();
	function __construct ($ID = NULL) {
		parent::__construct ($ID);
		if ($this->ID > 0) {
			$this->components = section_component::select (array ('section_ID' => $this->ID), array ('order' => 'ASC'))->bind (
				function ($obj) {
					$component = new component ($obj->component_ID);
					$class = 'XOMBO\\' . $component->type;
					return new $class ($component->ref_ID);
				}
			);
		}
	}
}
