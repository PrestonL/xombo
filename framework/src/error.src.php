<?php
function handle_error ($num, $str) {
	switch ($num) {
		case E_WARNING:
		case E_NOTICE:
			throw new exception ($str, $num);
			return TRUE;
			break;
		default:
			return FALSE;
	}
}

set_error_handler ("handle_error");