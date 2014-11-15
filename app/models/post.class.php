<?php
use XOMBO\post_author as post_author;
use XOMBO\author as author;
use XOMBO\post_category as post_category;
use XOMBO\post_section as post_section;
use XOMBO\event as event;
use XOMBO\post_event as post_event;
use XOMBO\post_location as post_location;
class post extends XOMBO\dbModelDefaults {
	public $url;
	public $authors = array ();
	public $categories = array ();
	public $locations = array ();
	public $events = array ();
	public $sections = array ();
	function __construct ($ID = NULL, $site_post_ID) {
		parent::__construct ($ID);
		if ($this->ID > 0) {
			$this->url = "/site/post/" . $site_post_ID;
			$this->authors = post_author::select (array ('post_ID' => $this->ID))->bind (
				function ($obj) {
					return new author ($obj->author_ID);
				}
			);
			$this->categories = post_category::select (array ('post_ID' => $this->ID))->bind (
				function ($obj) {
					return new category ($obj->category_ID);
				}
			);
			$this->events = post_event::select (array ('post_ID' => $this->ID), array ('order' => 'ASC'))->bind (
				function ($obj) {
					return new event ($obj->event_ID);
				}
			);
			$this->locations = post_location::select (array ('post_ID' => $this->ID), array ('order' => 'ASC'))->bind (
				function ($obj) {
					return new location ($obj->location_ID);
				}
			);
			$this->sections = post_section::select (array ('post_ID' => $this->ID), array ('order' => 'ASC'))->bind (
				function ($obj) {
					return new section ($obj->section_ID);
				}
			);
		}
	}
}
