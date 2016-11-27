<?php
$vendor_path = '../vendor/';
if (PHP_SAPI == 'cli') { // we start minion from a level up
  $vendor_path = 'vendor/';
}

$paths = array(
  /**
   * The directory in which your application specific resources are located.
   * The application directory must contain the bootstrap.php file.
   *
   * @link http://kohanaframework.org/guide/about.install#application
   */
  'APPPATH' => '../application',
  /**
   * The directory in which your modules are located.
   *
   * @link http://kohanaframework.org/guide/about.install#modules
   */
  'MODPATH' => '../modules',
  /**
   * The directory in which the Kohana resources are located. The system
   * directory must contain the classes/kohana.php file.
   *
   * @link http://kohanaframework.org/guide/about.install#system
   */
  'SYSPATH' => $vendor_path.'kohana/core',
);

/**
 * The default extension of resource files. If you change this, all resources
 * must be renamed to use the new extension.
 *
 * @see  http://kohanaframework.org/guide/about.install#ext
 */
define('EXT', '.php');

/**
 * End of standard configuration! Changing any of the code below should only be
 * attempted by those with a working knowledge of Kohana internals.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 */

// Set the full path to the docroot
define('DOCROOT', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

// For each path set
foreach ($paths as $key => $path)
{
  // Make the path relative to the docroot, for symlink'd index.php
  if ( ! is_dir($path) AND is_dir(DOCROOT.$path))
  {
    $path = DOCROOT.$path;
  }

  // Define the absolute path
  define($key, realpath($path).DIRECTORY_SEPARATOR);
}

if (file_exists('install'.EXT))
{
  // Load the installation check
  return include 'install'.EXT;
}

/**
 * Define the start time of the application, used for profiling.
 */
if ( ! defined('KOHANA_START_TIME'))
{
  define('KOHANA_START_TIME', microtime(TRUE));
}

/**
 * Define the memory usage at the start of the application, used for profiling.
 */
if ( ! defined('KOHANA_START_MEMORY'))
{
  define('KOHANA_START_MEMORY', memory_get_usage());
}

// Bootstrap the application
require APPPATH.'bootstrap'.EXT;

if (PHP_SAPI == 'cli') // Try and load minion
{
  class_exists('Minion_Task') OR die('Please enable the Minion module for CLI support.');
  set_exception_handler(array('Minion_Exception', 'handler'));

  Minion_Task::factory(Minion_CLI::options())->execute();
}
else
{
  /**
   * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
   * If no source is specified, the URI will be automatically detected.
   */
  echo Request::factory(TRUE, array(), FALSE)
    ->execute()
    ->send_headers(TRUE)
    ->body();
}
