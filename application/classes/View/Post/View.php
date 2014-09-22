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
 * Post view controller.
 **/
class View_Post_View extends View_Layout {
  /**
   * Post tag models.
   **/
  public $tags;
  /**
   * Post ID
   **/
  public $id;
  public $date;
  public $is_admin = FALSE;

  public $scripts = array(
    'autosize',
    'load_comment_form.js',
    'load_comments.js',
    'https://yandex.st/share/share.js'
  );

  public function get_tags()
  {
    $output = 'Теги: ';
    if (count($this->tags) > 0)
    {
      $i = 0;
      foreach ($this->tags as $tag)
      {
        if ($i > 0) $output .= ', ';
        $output .= '<a href="'.URL::site('tag/view/'.$tag->id).'"><span property="keywords">'.$tag->name.'</span></a>';
        $i++;
      }
    }
    else 
    {
      return '';
    }
    return $output;
  }

  public function link_edit()
  {
    return HTML::anchor(Route::url('default',array('controller' => 'Post', 'action' => 'edit', 'id' => $this->id)), 'Редактировать');
  }

  public function load_comment_form_action()
  {
    return Route::url('default', array('controller' => 'Comment', 'action' => 'form'));
  }

  public function load_comments_action()
  {
    return Route::url('default', array('controller' => 'Comment', 'action' => 'view', 'id' => $this->id));
  }
}
