<?php defined('SYSPATH') or die('No direct script access.'); 
 
class Form extends Kohana_Form {
  public static function orm_input($model, $name, array $attributes = NULL)
  {
    $html = '<div class="container">';
    $html .= self::label($name, $model->get_label($name));
    $html .= self::input($name,$model->get($name), $attributes);
    $html .= '</div>';
    return $html;
  }
  public static function orm_textarea($model, $name, array $attributes = NULL)
  {
    $html = '<div class="container">';
    $html .= self::label($name, $model->get_label($name));
    $html .= self::textarea($name,$model->get($name), $attributes);
    $html .= '</div>';
    return $html;
  }
}
