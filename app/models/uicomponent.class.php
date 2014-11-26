<?php
class uicomponent extends XOMBO\dbModelDefaults {
	public $section_component_ID = 0;
	public function __construct ($ID = NULL, $section_component_ID = NULL) {
		$this->section_component_ID = $section_component_ID;
		return parent::__construct ($ID);
	}	
}