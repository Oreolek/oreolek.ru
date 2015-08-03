<?php defined('SYSPATH') OR die('No direct script access.');
/*
 *  Personal site oreolek.ru source code
 *  Copyright (C) 2015 Alexander Yakovlev
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
 * Redefined HTML helper for HTML Tidy inclusion.
 **/
class Text extends Kohana_Text {
  /**
   * Approximate text length by minutes to read it.
   * @param string $html
   * @return number
   **/
  public static function time_to_read($html)
  {
    // ~900 chars per minute is average reading speed, according to Wikipedia
    return ceil(mb_strlen(strip_tags($html)) / 900);
  }
}
