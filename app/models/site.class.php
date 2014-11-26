<?php
use XOMBO\site_post as site_post;
use XOMBO\site_post_view as site_post_view;
use XOMBO\post_category_view as post_category_view;
use XOMBO\site_menu as site_menu;
class site extends XOMBO\dbModelDefaults {
	public $admin = 0;
	public $images = array ();
	public $navigation = array ('next' => '', 'previous' => '', 'menus' => array ());
	public $posts = array ();
	function __construct ($ID = NULL) {
		parent::__construct ($ID);
		if ($this->ID > 0) {
			try {
				session_start ();
				$this->admin = array_key_exists ('admin', $_SESSION) && $_SESSION['admin'] == 1 ? 1 : 0;
			} catch (exception $e) {
				// ignore
			}
			$this->images = image::select (array ('ID' => $this->image_ID));
			$this->navigation = array (
				'next' => '',
				'previous' => '',
				'menus' => site_menu::select (array ('site_ID' => $this->ID), array ('order' => 'ASC'))->bind (
					function ($obj) {
						return new menu ($obj->menu_ID);
					}
				)
			);
		}
	}
	public function nextUrl ($url) {
		$this->navigation['next'] = $url;
		return $this;
	}
	public function previousUrl ($url) {
		$this->navigation['previous'] = $url;
		return $this;
	}
	public function postById ($site_post_ID) {
		$site_post = new site_post_view ($site_post_ID);
		if ($site_post->ID > 0) {
			$this->posts = new post ($site_post->post_ID, $site_post->ID);
			if ($this->posts->ID > 0) {
				$query = XOMBO\DB::query (
					'SELECT `ID` FROM `site_post_view` 
					WHERE `site_ID`="' . XOMBO\DB::escape ($this->ID) . '" 
					AND `published` > "' . XOMBO\DB::escape ($site_post->published) . '" 
					ORDER BY `published` ASC LIMIT 1');
				if ($query && $query->num_rows) {
					$this->previousUrl (
						'/site/post/' . array_key ($query->fetch_assoc (), 'ID')
					);
				}
				$query = XOMBO\DB::query (
					'SELECT `ID` FROM `site_post_view` 
					WHERE `site_ID`="' . XOMBO\DB::escape ($this->ID) . '" 
					AND `published` < "' . XOMBO\DB::escape ($site_post->published) . '" 
					ORDER BY `published` DESC LIMIT 1');
				if ($query && $query->num_rows) {
					$this->nextUrl (
						'/site/post/' . array_key ($query->fetch_assoc (), 'ID')
					);
				}
			}
		}
		return $this;
	}
	public function allPosts ($limit = 5, $from = 0) {
		return $this->postsByFilter (array (), $limit, $from);
	}
	public function postsByFilter ($filter = array (), $limit = 5, $from = 0) {
		$filter['site_ID'] = $this->ID;
		$this->posts = site_post_view::select ($filter, array ('published' => 'DESC'), $limit, $from)->bind (
			function ($obj) {
				return new post ($obj->post_ID, $obj->ID);
			}
		);
		return 
			$this->nextUrl (
				$this->posts->count () >= $limit ? '/site/all/' . $limit . '/' . ($from + $limit) : ''
			)->previousUrl (
				$from > 0 ? '/site/all/' . $limit . '/' . ($from - $limit) : ''
			);
	}
	public function postsByCategory ($category, $limit = 5, $from = 0) {
		$this->posts = post_category_view::select (array ('site_ID' => $this->ID, 'category_ID' => $category), array ('published' => 'DESC'), $limit, $from)->bind (
			function ($obj) {
				return new post ($obj->post_ID, $obj->ID);
			}
		);
		return 
			$this->nextUrl (
				$this->posts->count () >= $limit ? '/site/category/' . $category . '/' . $limit . '/' . ($from + $limit) : ''
			)->previousUrl (
				$from > 0 ? '/site/category/' . $category .'/' . $limit . '/' . ($from - $limit) : ''
			);
	}
	public function createPost () {
		$post = new post ();
		$post->name = "New Post";
		$post->save ();
		if ($post->ID > 0) {
			$site_post = new site_post ();
			$site_post->site_ID = $this->ID;
			$site_post->post_ID = $post->ID;
			$site_post->published = date ("Y-m-d H:i:s");
			$site_post->save ();
			if ($site_post->ID > 0) {
				$post->site_post_ID = $site_post->ID;
				return $site_post;
			}
			$post->delete ();
			throw new exception ("Failed to add post to site.");
		}
		throw new exception ("Failed to create post");
	}
}
