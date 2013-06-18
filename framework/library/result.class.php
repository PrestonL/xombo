<?php
/**
 * XOMBO
 * result.class.php an iterator class for loading objects from the database incrementally
 *
 * @license Apache License V2 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * @author Nolte Burke
 * 	
 * 	Copyright 2013 Nolte Burke
 * 	
 * 	Licensed under the Apache License, Version 2.0 (the "License");
 * 	you may not use this file except in compliance with the License.
 * 	You may obtain a copy of the License at
 * 	
 * 		http://www.apache.org/licenses/LICENSE-2.0
 * 	
 * 	Unless required by applicable law or agreed to in writing, software
 * 	distributed under the License is distributed on an "AS IS" BASIS,
 * 	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * 	See the License for the specific language governing permissions and
 * 	limitations under the License.
 */
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
