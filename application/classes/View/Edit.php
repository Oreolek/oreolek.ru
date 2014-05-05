<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Edit view controller
 **/
class View_Edit extends View_Layout {
  public $model;
  public $errors;
  public $scripts = array(
    'jquery',
    'jquery.autosize-min.js',
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
