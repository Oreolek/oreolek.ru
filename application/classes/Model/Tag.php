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
 * Tag model.
 **/
class Model_Tag extends ORM {
  /**
   * @return array validation rules
   **/
  public function rules()
	{
		return array(
      'name' => array(
				array('not_empty'),
      ),
		);
	}

  protected $_has_many = array(
    'posts' => array(
      'model' => 'Post',
      'through' => 'posts_tags'
    )
  );

  public $_labels = array(
    'name' => 'Название',
    'description' => 'Описание'
  );

}
