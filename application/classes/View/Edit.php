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
 * Edit view controller. It's a universal form page for every CRUD imaginable.
 **/
class View_Edit extends View_Layout {
  public $model;
  public $errors;
  public $scripts = array(
    'autosize',
  );
  /**
   * Array of ORM inputs as:
   * field => control type
   **/
  public $controls = array();
  /**
   * Array of custom inputs as:
   * field => array(
   *   'label' => label
   *   'type' => control type
   *   'value' => current value
   * )
   **/
  public $custom_controls = array();
  public function get_controls()
  {
    $output = '';
    foreach ($this->controls as $key => $value)
    {
      $output .= $this->error($key);
      $output .= Form::orm_input($this->model, $key, $value);
    }
    foreach ($this->custom_controls as $key => $value)
    {
      if (!isset($value['value']))
      {
        $value['value'] = '';
      }
      $output .= '<div class="container">'.Form::label($key, $value['label']);
      $input = '';
      $input .= $this->error($key);
      switch($value['type'])
      {
        case 'file':
          $input = Form::file($key);
          break;
        case 'check':
        case 'chck':
        case 'checkbox':
          $input = Form::checkbox($key, $value['value']);
          break;
        case 'password':
          $input = Form::password($key, $value['value']);
          break;
        case 'text':
        case 'textarea':
          $input = Form::textarea($key, $value['value']);
          break;
        default:
          $input = Form::input($key,$value['value']);
      }
      $output .= $input.'</div>';
    }
    $output .= Form::btn_submit('Отправить');
    return $output;
  }

  /**
   * Show all errors for a field
   **/
  protected function error($key)
  {
    $error = Arr::get($this->errors, $key);
    return '<div class="bg-warning">'.$error.'</div>';
  }
}
