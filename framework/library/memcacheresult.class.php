<?php
class memcacheResult {
	private $result;
	public $num_rows;
	public function __construct (&$result) {
		$this->result = array ();
		while ($row = $result->fetch_assoc ()) {
			$this->result[] = $row;
		}
		$this->num_rows = count ($this->result);
		$result->data_seek (0);
	}
	public function data_seek ($pos) {
		return;
	}
	public function free () {
		return;
	}
	public function fetch_assoc () {
		return array_shift ($this->result);
		return false;
	}
}