<?php
use XOMBO\section_component as section_component;
use XOMBO\component as component;
class section extends XOMBO\dbModelDefaults {
	public $post_section_ID = 0;
	public $components = array ();
	function __construct ($ID = NULL, $post_section_ID = NULL) {
		parent::__construct ($ID);
		if ($this->ID > 0) {
			$this->post_section_ID = $post_section_ID;
			$this->components = section_component::select (array ('section_ID' => $this->ID), array ('order' => 'ASC'))->bind (
				function ($obj) {
					$component = new component ($obj->component_ID);
					return new $component->type ($component->ref_ID, $obj->ID);
				}
			)->getArray ();
		}
	}
	public function createHeading () {
		return $this->createComponent ("heading");
	}
	public function createP () {
		return $this->createComponent ("p");
	}
	public function createAside () {
		return $this->createComponent ("aside");
	}
	public function createFigure () {
		return $this->createComponent ("figure");
	}
	public function createNav () {
		return $this->createComponent ("nav");
	}
	public function createUl () {
		return $this->createComponent ("ul");
	}
	public function createOl () {
		return $this->createComponent ("ol");
	}
	private function createComponent ($type) {
		$c = new $type ();
		if ($c->hasField ('content'))
			$c->content = $type . ' content.';
		$c->published = date ("Y-m-d H:i:s");
		$c->save ();
		if ($c->ID > 0) {
			$component = new component ();
			$component->type = $type;
			$component->ref_ID = $c->ID;
			$component->save ();
			if ($component->ID > 0) {
				$section_component = new section_component ();
				$section_component->section_ID = $this->ID;
				$section_component->component_ID = $component->ID;
				$section_component->order = count ($this->components) + 1;
				$section_component->save ();
				if ($section_component->ID > 0) {
					return $c;
				}
				$component->delete (); $c->delete ();
				throw new exception ("Could not add component to section.");
			}
			$c->delete ();
			throw new exception ("Could not add " . $type . " component.");
		}
	}
}
