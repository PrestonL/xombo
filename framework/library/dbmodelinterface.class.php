<?php
interface dbModelInterface {
	static function getTable ();
	static function isCacheable ();
}