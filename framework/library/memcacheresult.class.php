<?php
class memcacheResult {
	private $result;
	public function __construct (&$result) {
		$this->result = array ();
		while ($row = $result->fetch_assoc ()) {
			$this->result[] = $row;
		}
		$result->data_seek (0);
	}
	public function fetch_assoc () {
		if (count ($this->result))
			return array_shift ($this->result);
		return false;
	}
}