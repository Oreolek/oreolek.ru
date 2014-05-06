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
 * Comment panel view controller. Only for administrator.
 **/
class View_Comment_Index extends View_Index {
  public $scripts = array(
    'https://yandex.st/jquery/2.0.3/jquery.min.js',
    'comment_buttons.js'
  );

  /**
   * An internal function to prepare item data.
   **/
  protected function show_item($item)
  {
    $output = array(
        'date' => $item->posted_at,
        'author_email' => $item->author_email,
        'content' => Markdown::instance()->transform($item->content),
        'author_name' => '',
        'is_approved' => '',
        'comment_id' => $item->id,
        'post_link' => Route::url('default', array('controller' => 'Post', 'action' => 'view', 'id' => $item->post)),
        'edit_link' => Route::url('default', array('controller' => 'Comment', 'action' => 'edit', 'id' => $item->id)),
        'delete_link' => Route::url('default', array('controller' => 'Comment', 'action' => 'delete','id' => $item->id)),
    );
    if (empty($item->author_name))
    {
      $output['author_name'] = Kohana::$config->load('common')->get('anonymous_name');
    }
    else
    {
      $output['author_name'] = $item->author_name;
    }
    $output['is_approved'] = Form::checkbox(
      'is_approved',
      $item->id,
      $item->is_approved == Model_Comment::STATUS_APPROVED,
      array(
        'data-edit-url' => Route::url('default', array('controller' => 'Comment','action' => 'edit','id' => $item->id)),
      )
    );
    return $output;
  }


}
