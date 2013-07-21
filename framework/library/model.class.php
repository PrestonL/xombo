<?php
/**
 * XOMBO
 * model.class.php all models inherit from this core class
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
abstract class model {
	private $properties;
	private $validators;
	private $hiddenFields;

	protected function &addField ($name, $default = NULL, $validator = NULL) {
		if (!is_array ($this->properties))
			$this->properties = array ();
		if (!is_array ($this->validators))
			$this->validators = array ();
		if (!array_key_exists ($name, $this->properties) && !array_key_exists ($name, $this->validators)) {
			$this->validators[$name] = !is_string ($validator) ? NULL : (string) $validator;
			$this->properties[$name] = $this->validate ($name, $default);
		}
		return $this;
	}

	protected function &delField ($name) {
		if (is_array ($this->properties) && array_key_exists ($name, $this->properties)) {
			unset ($this->properties[$name]);
			unset ($this->validators[$name]);
			return $this;
		}
		throw new \exception ("Property does not exist");
		return $this;
	}

	protected function &hideField ($name) {
		if (is_array ($this->properties) && array_key_exists ($name, $this->properties)) {
			$this->hiddenFields[$name] = true;
			return $this;
		}
		throw new \exception ("Property does not exist");
		return $this;
	}

	protected function getFields () {
		return is_array ($this->properties) ? $this->properties : array ();
	}

	protected function getPublicFields () {
		if (!is_array ($this->hiddenFields))
			$this->hiddenFields = array ();
		return array_diff_key ($this->getFields (), $this->hiddenFields);
	}
	
	protected function hasField ($name, $properties = true) {
		$fields = $this->getFields ();
		if ($properties) {
			$fields = array_merge ($fields, get_object_vars ($this));
		}
		if (array_key_exists ($name, $fields))
			return true;
		return false;
	}

	protected function validate ($name, $value) {
		$ret = call_user_func (get_class ($this) . "::" . (array_key_exists ($name, $this->validators) && !is_null ($this->validators[$name]) ? $this->validators[$name] : "validatorDefault"), $this, $name, $value);
		return $ret;
	}

	protected static function validatorDefault ($obj, $field, $value) { // validator prototype
		if (is_model ($obj) && (is_string ($value) || is_numeric ($value) || is_bool ($value)))
			return $value;
		return NULL;
	}

	public function __get ($name) {
		if (is_array ($this->properties) && array_key_exists ($name, $this->properties))
			return $this->properties[$name];
		return NULL;
	}

	public function __set ($name, $value) {
		if (is_array ($this->properties) && array_key_exists ($name, $this->properties)) {
			$old = $this->properties[$name];
			$this->properties[$name] = $this->validate ($name, $value);
			if (!is_null ($this->properties[$name])) return TRUE;
			$this->properties[$name] = $old;
			throw new \exception ("You tried to set " . get_class ($this) . "'s property " . $name . " to " . $value . ", which did not validate. You may have entered something incorrectly. Click back and try again.");
		}
		throw new \exception ("You tried to set a value to " . $name . " when " . $name . " does not exist as a field in this context.");
		return FALSE;
	}
}
