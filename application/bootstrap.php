<?php defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/Kohana/Core'.EXT;

if (is_file(APPPATH.'classes/Kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/Kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/Kohana'.EXT;
}

/**
 * Set the default time zone.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('Asia/Novosibirsk');

/**
 * Set the default locale.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'ru_RU.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://kohanaframework.org/guide/using.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('ru');

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV']))
{
	Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
 'base_url'   => '/',
 'index_file' => false,
 'errors'     => TRUE,
 'profile'    => (Kohana::$environment == Kohana::DEVELOPMENT), 
 'caching'    => (Kohana::$environment == Kohana::PRODUCTION) 
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Set cookie salt (required)
 */
Cookie::$salt = 'Dz75cE_TgeDXvo2';

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	 'auth'          => MODPATH.'auth',              // Basic authentication
	 'database'      => MODPATH.'database',          // Database access
	 'orm'           => MODPATH.'orm',               // Object Relationship Mapping
	 'markdown'      => MODPATH.'markdown',          // Markdown module
   'less'          => MODPATH.'less',              // LEaner CSS
   'debug-toolbar' => MODPATH.'debug-toolbar',     // Debug toolbar
	 'image'         => MODPATH.'image',             // Image manipulation
	 'kostache'      => MODPATH.'kostache',          // Logic-less Mustache views
	 'unittest'      => MODPATH.'unittest',          // Unit testing
	 'cache'         => MODPATH.'cache',             // Caching with multiple backends
	//  'codebench'  => MODPATH.'codebench',         // Benchmarking tool
	 'sitemap'       => MODPATH.'sitemap',           // Sitemap generator
	));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
Route::set('sitemap_index', 'sitemap.xml(<gzip>)', array('gzip' => '\.gz'))
	->defaults(array(
		'controller' => 'Sitemap',
		'action' => 'index'
	));


Route::set('error', 'error/<action>(/<message>)', array('action' => '[0-9]++','message' => '.+'))
 ->defaults(array( 
  'controller' => 'Error',
));

Route::set('default', '(<controller>(/<action>(/<id>)(/page/<page>)))')
 ->defaults(array(
  'controller' => 'Post',
  'action'     => 'read',
 ));
