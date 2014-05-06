#!/usr/bin/php
<?php
$user_data = json_decode (file_get_contents ("http://169.254.169.254/latest/user-data"));
if (property_exists ($user_data, "key") && property_exists ($user_data, "secret")) {
	echo "-O " . $user_data->key . " -W " . $user_data->secret;
}
