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
 * Edit post view controller
 **/
class View_Post_Edit extends View_Edit {
  public $scripts = array(
    'jquery',
    'jquery.autosize-min.js',
    'lightbox-2.6.min.js',
    'autosave.js'
  );
  public $tags;

  public function input_name()
  {
    return Form::orm_input($this->model, 'name');
  }
  public function input_password()
  {
    return Form::orm_input($this->model, 'password', 'input');
  }

  public function input_is_draft()
  {
    return Form::orm_input($this->model, 'is_draft', 'checkbox');
  }

  public function input_content()
  {
    return Form::orm_input($this->model, 'content', 'textarea');
  }

  public function input_posted_at()
  {
    return Form::orm_input($this->model, 'posted_at', 'date');
  }

  public function input_tags()
  {
    return Form::input('tags', $this->get_tags(), array('label' => 'Теги'));
  }

  public function input_buttons()
  {
    $output = '';
    $output .= Form::btn_submit('Сохранить и закончить редактирование');
    $output .= Form::ajax_submit('preview','Предпросмотр');
    if ($this->model->loaded())
    {
      $output .= Form::ajax_submit('save','Сохранить');
    }
    return $output;
  }
   
  protected function get_tags()
  {
    $output = '';
    if (count($this->tags) > 0)
    {
      $i = 0;
      foreach ($this->tags as $tag)
      {
        if ($i > 0) $output .= ', ';
        $output .= $tag->name;
        $i++;
      }
    }
    return $output;
  }
}
