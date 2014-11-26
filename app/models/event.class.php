<?php
class event extends ratable {
	public $duration;
	function __construct ($ID = NULL) {
		parent::__construct ($ID);
		if ($this->ID > 0) {
			$this->duration = strtotime ($this->end) - strtotime ($this->start);
			$s = $this->duration % 60;
			$this->duration = floor ($this->duration / 60);
			$m = $this->duration % 60;
			$this->duration = floor ($this->duration / 60);
			$h = $this->duration % 24;
			$this->duration = floor ($this->duration / 24);
			$d = $this->duration;
			$this->duration = ($d ? $d . 'd ' : '') . ($h ? $h . 'h ' : '') . ($m ? $m . 'm ' : '') . ($s ? $s . 's' : '');
		}
	}
}
