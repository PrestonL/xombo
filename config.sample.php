<?php
// site configuration
define ('SITE_DOMAIN', 'framework.xombo.com');

// database configuration
define ('DB_USERNAME',	'root');
define ('DB_PASSWORD',	'');
define ('DB_HOSTNAME',	'p:localhost');
define ('DB_SCHEMA',	'xombo');

// memcache configuration
define ('MEMCACHE_ENABLED', false);
define ('MEMCACHE_HOSTNAME', 'localhost');

// session configuration
define ('SESSION_COOKIE', 'AUTHKEY');
define ('SESSION_LIFETIME', 31556926); // 1 year
define ('SESSION_DOMAIN', '.' . (array_key_exists ('HTTP_HOST', $_SERVER) ? $_SERVER['HTTP_HOST'] : SITE_DOMAIN));
define ('SESSION_PATH', '/');

// display uncaught errors
ini_set ('display_errors', true);

// Timezone
date_default_timezone_set ('America/Vancouver');
