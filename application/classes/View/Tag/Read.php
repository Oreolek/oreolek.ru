<?php defined('SYSPATH') or die('No direct script access.');
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
 * Tag overview view controller.
 **/
class View_Tag_Read extends View_Read {
  public $feed_link;
  public $tag_name;
  
  /**
   * RSS feed
   **/
  public function feeds()
  {
    return array(
      array(
        'title' => 'Свежие записи с меткой «'.$this->tag_name.'»',
        'url' => $this->feed_link,
      ),
    );
  }
}
