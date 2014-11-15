<?php
class location extends XOMBO\dbModelDefaults {
	public $url;
	function __construct ($ID = NULL) {
		parent::__construct ($ID);
		if ($this->ID > 0) {
			$this->url = "/site/map/" . $this->ID;
		}
	}
}
