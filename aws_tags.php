#!/usr/bin/php
<?php
$tags = file_get_contents ($argv[$argc-1]);
$tags = explode ("\n", $tags);
$tarray = array ();
foreach ($tags as $tag) {
	if (strlen ($tag)) {
		$tag = preg_split ("/[\s,]+/", $tag, 5);
		$tarray[$tag[3]] = $tag[4];
	}
}
file_put_contents ("/tmp/tags.json", json_encode ($tarray));
