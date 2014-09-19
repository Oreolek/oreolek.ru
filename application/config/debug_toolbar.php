<?php defined('SYSPATH') or die('No direct script access.');

$config['auto_render'] = Kohana::$environment === Kohana::DEVELOPMENT;
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
	'docs/media',  // Userguide media route
  'sitemap', // Sitemaps
  'sitemap_index' // Main sitemap
);

return $config;
