<?php
/**
 * XOMBO
 * memcacheresult.class.php an object used to normalize the interface between memcache data and the database so queries are easier to cache
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
namespace XOMBO;
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