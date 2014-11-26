<?php
use XOMBO\rating as rating;
class ratable extends XOMBO\dbModelDefaults {
	public $ratings = array ();
	function __construct ($ID = NULL) {
		parent::__construct ($ID);
		if ($this->ID > 0) {
			if ($this->rating_ID > 0) {
				$this->ratings = rating::select (array ('ID' => $this->rating_ID), 1);
			}
		}
	}
}
