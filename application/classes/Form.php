<?php defined('SYSPATH') or die('No direct script access.'); 
 
class Form extends Kohana_Form {
  public static function orm_input($model, $name, $type, array $attributes = NULL)
  {
    switch($type)
    {
      case 'check':
      case 'chck':
      case 'checkbox':
        return self::orm_checkbox($model, $name, $attributes);
      case 'password':
        return self::orm_password($model, $name, $attributes);
      case 'text':
      case 'textarea':
        return self::orm_textarea($model, $name, $attributes);
      default:
        return self::orm_textinput($model, $name, $attributes);
    }
  }
  public static function orm_textinput($model, $name, array $attributes = NULL)
  {
    $html = '<div class="container">';
    $html .= self::label($name, $model->get_label($name));
    $html .= self::input($name,$model->get($name), $attributes);
    $html .= '</div>';
    return $html;
  }
  public static function orm_password($model, $name, array $attributes = NULL)
  {
    $html = '<div class="container">';
    $html .= self::label($name, $model->get_label($name));
    $html .= self::password($name,$model->get($name), $attributes);
    $html .= '</div>';
    return $html;
  }
  public static function orm_textarea($model, $name, array $attributes = NULL)
  {
    $html = '<div class="container">';
    $html .= self::label($name, $model->get_label($name));
    $html .= self::textarea($name,$model->get($name), $attributes, FALSE);
    $html .= '</div>';
    return $html;
  }

  /**
   * Checkbox ORM generation.
   * Assumes $name a boolean attribute
   **/
  public static function orm_checkbox($model, $name, array $attributes = NULL)
  {
    $html = '<div class="container">';
    $html .= self::label($name, $model->get_label($name));
    $html .= self::checkbox($name, 1, (boolean) $model->get($name), $attributes);
    $html .= '</div>';
    return $html;
  }

  public static function ajax_submit($name, $value)
  {
    return '<input type="button" name="'.$name.'" value="'.$value.'" onclick="ajax_submit(\''.$name.'\')"></input>';
  }

}
