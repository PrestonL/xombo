<?php
class result implements iterator {
	private $classname;
	private $data;
	private $position;
	private $length;
	public function __construct ($class, $data) {
		$this->classname = $class;
		$this->data = $data;
		$this->position = 0;
		$this->length = $data->num_rows;
	}
	public function rewind () {
		$this->position = 0;
		$this->data->data_seek (0);
	}
	public function current () {
		return call_user_func ($this->classname . '::factory', array_key ($this->data->fetch_assoc (), 'ID'));
	}
	public function __destruct () {
		$this->data->free ();
	}
	public function key () {
		return $this->position;
	}
	public function next () {
		$this->position++;
		return $this;
	}
	public function valid () {
		return $this->position < $this->length ? true : false;
	}
}
