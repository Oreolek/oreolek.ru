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
 * Blog post model.
 **/
class Model_Post extends ORM {
  /**
   * @return array validation rules
   **/
  public function rules()
  {
    return array(
      'name' => array(
	array('not_empty'),
      ),
      'content' => array(
	array('not_empty'),
	array('min_length', array(':value', 4)),
      ),
      'draft' => array(
        array('numeric')
      ),
      'posted_at' => array(
        array('date')
      ),
    );
  }

  protected $_has_many = array(
    'comments' => array(
      'model' => 'Comment',
      'foreign_key' => 'post_id'
    ),
    'tags' => array(
      'model' => 'Tag',
      'through' => 'posts_tags'
    )
  );

  /**
   * Array of field labels.
   * Used in forms.
   **/
  protected $_labels = array(
    'name' => 'Заголовок',
    'content' => 'Текст записи',
    'is_draft' => 'Черновик',
    'posted_at' => 'Дата',
    'password' => 'Пароль для расшифровки',
    'updated_at' => 'Дата изменения',
  );

  /**
   * Search term in all posts using Sphinx.
   * Note that Sphinx enforces hidden LIMIT 1,20
   **/
  public static function search($term)
  {
    $table = Kohana::$config->load('database')->get('sphinx')['connection']['database'];
    $db = Database::instance('sphinx');
    $result = $db->query(Database::SELECT, 'SELECT id FROM `'.$table.'` WHERE MATCH('.$db->quote($term).') LIMIT 100');
    return $result->as_array(NULL, 'id');
  }

  public static function get_latest_date()
  {
    $query = DB::select(array(DB::expr('MAX(`posted_at`)'), 'max_date'))->from('posts');
    return $query->execute()->get('max_date');
  }
  
  public static function get_latest_change()
  {
    $query = DB::select(array(DB::expr('MAX(`updated_at`)'), 'max_date'))->from('posts');
    return $query->execute()->get('max_date');
  }

  /**
   * Returns array of ids and posted_at timestamps.
   * Used in sitemap generation.
   **/
  public static function get_dates()
  {
    $query = DB::select('id', 'posted_at')->from('posts');
    return $query->execute()->as_array('id', 'posted_at');
  }
}
