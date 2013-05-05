<?php
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
		throw new exception ("Property does not exist");
		return $this;
	}

	protected function &hideField ($name) {
		if (is_array ($this->properties) && array_key_exists ($name, $this->properties)) {
			$this->hiddenFields[$name] = true;
			return $this;
		}
		throw new exception ("Property does not exist");
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

	protected function validate ($name, $value) {
		return call_user_func (get_class ($this) . "::" . (array_key_exists ($name, $this->validators) && !is_null ($this->validators[$name]) ? $this->validators[$name] : "validatorDefault"), $this, $name, $value);
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
			throw new exception ("You tried to set " . get_class ($this) . "'s property " . $name . " to " . $value . ", which did not validate. You may have entered something incorrectly. Click back and try again.");
		}
		throw new exception ("You tried to set a value to " . $name . " when " . $name . " does not exist as a field in this context.");
		return FALSE;
	}
}
