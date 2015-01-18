<?php
use XOMBO\site_post as site_post;
use XOMBO\post_author as post_author;
use XOMBO\post_category as post_category;
use XOMBO\post_section as post_section;
use XOMBO\post_event as post_event;
use XOMBO\post_location as post_location;
use XOMBO\post_product as post_product;
class post extends XOMBO\dbModelDefaults {
	public $site_post_ID = 0;
	public $url;
	public $authors = array ();
	public $categories = array ();
	public $locations = array ();
	public $products = array ();
	public $events = array ();
	public $sections = array ();
	function __construct ($ID = NULL, $site_post_ID= NULL) {
		parent::__construct ($ID);
		if ($this->ID > 0) {
			$this->site_post_ID = $site_post_ID;
			if ($this->site_post_ID > 0) {
				$site_post = new site_post ($site_post_ID);
				$this->published = $site_post->published;
			}
			$this->url = "/site/post/" . $site_post_ID;
			$this->authors = post_author::select (array ('post_ID' => $this->ID))->bind (
				function ($obj) {
					return new author ($obj->author_ID);
				}
			)->getArray ();
			$this->categories = post_category::select (array ('post_ID' => $this->ID))->bind (
				function ($obj) {
					return new category ($obj->category_ID);
				}
			)->getArray ();
			$this->events = post_event::select (array ('post_ID' => $this->ID), array ('order' => 'ASC'))->bind (
				function ($obj) {
					return new event ($obj->event_ID);
				}
			)->getArray ();
			$this->locations = post_location::select (array ('post_ID' => $this->ID), array ('order' => 'ASC'))->bind (
				function ($obj) {
					return new location ($obj->location_ID);
				}
			)->getArray ();
			$this->products = post_product::select (array ('post_ID' => $this->ID), array ('order' => 'ASC'))->bind (
				function ($obj) {
					return new product ($obj->product_ID);
				}
			)->getArray ();
			$this->sections = post_section::select (array ('post_ID' => $this->ID), array ('order' => 'ASC'))->bind (
				function ($obj) {
					return new section ($obj->section_ID, $obj->ID);
				}
			)->getArray ();
		}
	}
	function createSection () {
		if ($this->ID > 0) {
			$section = new section ();
			$section->name = "default";
			$section->save ();
			if ($section->ID > 0) {
				$post_section = new post_section ();
				$post_section->post_ID = $this->ID;
				$post_section->section_ID = $section->ID;
				$post_section->order = count ($this->sections) + 1;
				$post_section->save ();
				if ($post_section->ID > 0) {
					return $post_section;
				}
				$section->delete ();
				throw new exception ("Unable to add section to post.");
			}
			throw new exception ("Unable to create section.");
		}
		throw new exception ("Unable to access post.");
	}
	function createEvent () {
		if ($this->ID > 0) {
			$event = new event ();
			$event->name = "default";
			$event->save ();
			if ($event->ID > 0) {
				$post_event = new post_event ();
				$post_event->post_ID = $this->ID;
				$post_event->event_ID = $event->ID;
				$post_event->order = count ($this->events) + 1;
				$post_event->save ();
				if ($post_event->ID > 0) {
					return $post_event;
				}
				$event->delete ();
				throw new exception ("unable to add event to post.");
			}
			throw new exception ("unable to create event");
		}
		throw new exception ("Unable to access post");
	}
	function createLocation () {
		if ($this->ID > 0) {
			$location = new location ();
			$location->name = "default";
			$location->save ();
			if ($location->ID > 0) {
				$post_location = new post_location ();
				$post_location->post_ID = $this->ID;
				$post_location->location_ID = $location->ID;
				$post_location->order = count ($this->locations) + 1;
				$post_location->save ();
				if ($post_location->ID > 0) {
					return $post_location;
				}
				$location->delete ();
				throw new exception ("unable to add location to post.");
			}
			throw new exception ("unable to create location");
		}
		throw new exception ("Unable to access post");
	}
	function createProduct () {
		if ($this->ID > 0) {
			$product = new product ();
			$product->name = "default";
			$product->save ();
			if ($product->ID > 0) {
				$post_product = new post_product ();
				$post_product->post_ID = $this->ID;
				$post_product->product_ID = $product->ID;
				$post_product->order = count ($this->products) + 1;
				$post_product->save ();
				if ($post_product->ID > 0) {
					return $post_product;
				}
				$product->delete ();
				throw new exception ("unable to add product to post.");
			}
			throw new exception ("unable to create product");
		}
		throw new exception ("Unable to access post");
	}
}
