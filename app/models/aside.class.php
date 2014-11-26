<?php
use XOMBO\aside_image as aside_image;
use XOMBO\aside_author as aside_author;
class aside extends uicomponent {
	public $images = array ();
	public $authors = array ();
	function __construct ($ID = NULL, $section_component_ID = NULL) {
		parent::__construct ($ID, $section_component_ID);
		if ($this->ID > 0) {
			$this->images = aside_image::select (array ('aside_ID' => $this->ID), 1)->bind (
				function ($obj) {
					return new image ($obj->image_ID);
				}
			);
			$this->authors = aside_author::select (array ('aside_ID' => $this->ID), 1)->bind (
				function ($obj) {
					return new author ($obj->author_ID);
				}
			);
		}
	}
	public static function getTable () {
		return "aside";
	}
}
