<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Migrator from some CMS to this engine
 **/
abstract class Model_Migrator extends Model {
  protected $source_database;
  public function __construct($hostname = false, $database = false, $username = false, $password = false)
  {
    if ($hostname) $this->set('hostname',$hostname);
    if ($database) $this->set('database',$database);
    if ($username) $this->set('username',$username);
    if ($password) $this->set('password',$password);
  }
  /**
   * Connect to database; returns true if connection is established
   **/
  public function connect()
  {
    $this->source_database = Database::instance('source', array(
      'type' => 'MySQL',
      'connection' => array(
        'hostname'   => $this->get('hostname'),
        'database'   => $this->get('database'),
        'username'   => $this->get('username'),
        'password'   => $this->get('password'),
        'persistent' => FALSE,
      ),
      'table_prefix' => '',
      'charset'      => 'utf8',
      'caching'      => FALSE,
      'profiling'    => FALSE,
    ));
    if ($this->source_database) return true;
    return false;
  }

  public function migrate_posts() {}
  public function migrate_pages() {}
  public function migrate_comments() {}

  protected $_labels = array(
    'database' => 'База данных',
    'hostname' => 'Хост',
    'username' => 'Имя пользователя',
    'password' => 'Пароль',
    'prefix' => 'Префикс'
  );
  protected $_attributes = array(
    'database' => NULL,
    'hostname' => NULL,
    'username' => NULL,
    'password' => NULL,
    'prefix' => 'wp_'
  );
  public function get_label($label)
  {
    return Arr::get($this->_labels, $label);
  }
  public function get($attribute)
  {
    return Arr::get($this->_attributes, $attribute);
  }
  public function set($attribute, $value)
  {
    if (!isset($attribute)) return false;
    $this->_attributes[$attribute] = $value;
    return true;
  }
}
