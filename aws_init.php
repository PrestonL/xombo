#!/usr/bin/php
<?php
$json = json_decode (shell_exec (dirname (__FILE__) . "/aws_tags.sh"), TRUE);
var_dump ($json);
// obtains aws tags for the instance and performs setup based on them
