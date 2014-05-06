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
 * Page model.
 **/
class Model_Page extends ORM {

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
      )
		);
	}

  /**
   * Array of field labels.
   * Used in forms.
   **/
  protected $_labels = array(
    'name' => 'Заголовок',
    'content' => 'Текст страницы',
    'is_draft' => 'Черновик'
  );

  /**
   * Returns array of ids and posted_at timestamps.
   * Used in sitemap generation.
   **/
  public static function get_ids()
  {
    $query = DB::select('id')->from('pages');
    return $query->execute()->as_array(NULL, 'id');
  }
}
