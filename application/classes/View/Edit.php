<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Контроллер вида оглавлений
 **/
class View_Edit extends View_Layout {
  public $model;
  /**
   * Array of ORM inputs as:
   * field => control type
   **/
  public $controls;
  /**
   * Array of custom inputs as:
   * field => array(
   *   'label' => label
   *   'type' => control type
   *   'value' => current value
   * )
   **/
  public $custom_controls;
  public function get_controls()
  {
    $output = '';
    foreach ($this->controls as $key => $value)
    {
      $output .= Form::orm_input($this->model, $key, $value);
    }
    foreach ($this->custom_controls as $key => $value)
    {
      $output .= '<div class="container">'.Form::label($key, $value['label']).Form::input($key,$value['value']).'</div>';
    }
    $output .= Form::submit('submit','Отправить');
    return $output;
  } 
}
