<?php
class cloud extends XOMBO\dbModelDefaults {
	public $apps;
	public function __construct ($ID = NULL) {
		parent::__construct ($ID);
		$this->apps = array ();
		if ($this->ID > 0) {
			$this->apps = XOMBO\cloudApp::select (array ('cloud_ID' => $this->ID))->bind (function ($obj) {
				return new app ($obj->app_ID);
			});
		}
	}
}
