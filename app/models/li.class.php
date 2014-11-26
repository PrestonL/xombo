<?php
class li extends XOMBO\dbModelDefaults {
	public $ul_li_ID = 0;
	public function __construct ($ID = NULL, $ul_li_ID = NULL) {
		parent::__construct ($ID);
		if ($this->ID > 0) {
			$this->ul_li_ID = $ul_li_ID;
		}
	}
}