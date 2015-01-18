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
	public function createImage () {
		$image = new image ();
		$image->src = "/images/blank.gif";
		$image->save ();
		$aside_image = new aside_image ();
		$aside_image->aside_ID = $this->ID;
		$aside_image->image_ID = $image->ID;
		$aside_image->save ();
		return $aside_image;
	}
	public function createAuthor () {
		$author = new author ();
		$author->firstname = "New";
		$author->lastname = "Author";
		$author->save ();
		$aside_author = new aside_author ();
		$aside_author->aside_ID = $this->ID;
		$aside_author->author_ID = $author->ID;
		$aside_author->save ();
		return $aside_author;		
	}
	public static function getTable () {
		return "aside";
	}
}
