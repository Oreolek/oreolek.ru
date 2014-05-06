<?php defined('SYSPATH') OR die('No direct access allowed.');
/*
 *  Personal site oreolek.ru source code
 *  Copyright (C) 2014 Alexander Yakovlev
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>. 
 */

/**
 * Migrator from some CMS to this engine. Currently only Wordpress implemented.
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
