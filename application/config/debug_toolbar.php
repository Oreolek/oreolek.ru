<?php defined('SYSPATH') or die('No direct script access.');

/*
 * Log toolbar data to FirePHP
 */
$config['firephp_enabled'] = FALSE;

/**
 * Exclude configs
 */
$config['skip_configs'] = array('database', 'encrypt');

/**
 * Disabled routes
 */
$config['excluded_routes'] = array(
	'docs/media'  // Userguide media route
);

return $config;
