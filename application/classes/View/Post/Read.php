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
 * Reading view controller.
 * Reading view prints out the content of items, not just the headings.
 * Reading view is suitable only for post items, but it can be used for tag reading.
 **/
class View_Post_Read extends View_Read {
  public $_view = 'read';
  public $show_date = TRUE;
  public $show_create = FALSE;
  
  public $scripts = array(
    'lightbox-2.6.min.js',
  );

  protected function show_item($item)
  {
    if (is_null($this->is_admin))
    {
      $this->is_admin = Auth::instance()->logged_in('admin');
    }
    $output = array(
        'date' => '',
        'heading' => '',
        'content' => '',
        'comment_count' => '',
        'edit_link' => '',
        'view_link' => '',
        'comments_link' => ''
    );
    if ($this->show_date)
    {
      $output['date'] = $item->creation_date();
    }
    if ($this->is_admin)
    {
      $output['edit_link'] = Route::url('default', array('controller' => Request::current()->controller(), 'action' => 'edit','id' => $item->id));
    }
    $output['view_link'] = Route::url('default', array('controller' => Request::current()->controller(), 'action' => 'view','id' => $item->id));
    $output['heading'] = $item->name;
    $output['comment_count'] = $item->comment_count;
    $output['comments_link'] = Route::url('default', array('controller' => Request::current()->controller(), 'action' => 'view','id' => $item->id)).'#comments';
    if (!empty($item->password))
    {
      $output['content'] = '<p>Закрытая запись. Доступ только по паролю.</p>';
    }
    else
    {
      // now limit words in content
      $output['content'] = Markdown::instance()->transform(Text::limit_words($item->content, Kohana::$config->load('common.brief_limit')));
      // but we have to close all unclosed tags
      $output['content'] = HTML::tidy($output['content']);
    }
    return $output;
  }
}
