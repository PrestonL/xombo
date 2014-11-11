<?php
class site extends XOMBO\dbModelDefaults {
	public $images;
	public $navigation;
	public $posts;
	function __construct ($ID = NULL) {
		parent::__construct ($ID);
		if ($this->ID > 0) {
			$this->images = XOMBO\image::select (array ('ID' => $this->image_ID));
			$this->posts = XOMBO\site_post::select (array ('site_ID' => $this->ID))->bind (
				function ($obj) {
					return new XOMBO\post ($obj->post_ID);
				}
			);
			$this->navigation = array (
				'next' => '',
				'previous' => '',
				'menus' => XOMBO\menu_sorted::select (array ('site_ID' => $this->ID))
			);
		}
		
	}
}
