<?php defined('SYSPATH') or die('No direct script access.'); 
/**
 * Redefined Form helper for Bootstrap-compliant and ORM generation.
 * @package    Oreolek 
 * @category   Helpers
 **/
 
class Form extends Kohana_Form {
  public static function orm_input($model, $name, $type)
  {
    switch($type)
    {
      case 'check':
      case 'chck':
      case 'checkbox':
        return self::orm_checkbox($model, $name);
      case 'password':
        return self::orm_password($model, $name);
      case 'city':
      case 'cityselect':
        return self::orm_cityselect($model, $name);
      case 'category':
      case 'catselect':
        return self::orm_catselect($model, $name);
      case 'text':
      case 'textarea':
        return self::orm_textarea($model, $name);
      case 'wysiwyg':
        return self::orm_wysiwyg($model, $name);
      case 'input_inline':
        return self::orm_textinput_inline($model, $name);
      case 'password_inline':
        return self::orm_password_inline($model, $name);
      case 'text_inline':
      case 'textarea_inline':
        return self::orm_textarea_inline($model, $name);
      default:
        return self::orm_textinput($model, $name);
    }
  }

  public static function orm_textinput($model, $name)
  {
    $template = new View_Form_Input;
    $template->name = $name;
    $template->label = __($model->get_label($name));
    $template->value = $model->$name;
    return self::render_control($template);
  }
  public static function input($name, $value = NULL, array $attributes = NULL)
  {
    $template = new View_Form_Input;
    $template->name = $name;
    $template->label = __(Arr::get($attributes, 'label'));
    $template->value = $value;
    return self::render_control($template);
  }
  public static function orm_password($model, $name)
  {
    $template = new View_Form_Password;
    $template->name = $name;
    $template->label = __($model->get_label($name));
    $template->value = $model->$name;
    return self::render_control($template);
  }
  public static function orm_textinput_inline($model, $name)
  {
    $template = new View_Form_Input;
    $template->_view = 'form/inline/input';
    $template->name = $name;
    $template->label = __($model->get_label($name));
    $template->value = $model->$name;
    return self::render_inline_control($template);
  }
  public static function orm_password_inline($model, $name)
  {
    $template = new View_Form_Password;
    $template->_view = 'form/inline/password';
    $template->name = $name;
    $template->label = __($model->get_label($name));
    $template->value = $model->$name;
    return self::render_inline_control($template);
  }
  public static function orm_textarea($model, $name)
  {
    $template = new View_Form_Textarea;
    $template->name = $name;
    $template->label = __($model->get_label($name));
    $template->value = $model->$name;
    return self::render_control($template);
  }
  public static function orm_textarea_inline($model, $name)
  {
    $template = new View_Form_Textarea;
    $template->_view = 'form/inline/textarea';
    $template->name = $name;
    $template->label = __($model->get_label($name));
    $template->value = $model->$name;
    return self::render_inline_control($template);
  }

  /**
   * A textarea with a HTML visual editor
   **/
  public static function orm_wysiwyg($model, $name)
  {
    $template = new View_Form_WYSIWYG;
    $template->name = $name;
    $template->label = __($model->get_label($name));
    $template->value = $model->$name;
    return self::render_control($template);
  }

  /**
   * Checkbox ORM generation.
   * Assumes $name a boolean attribute
   **/
  public static function orm_checkbox($model, $name)
  {
    return self::checkbox($name, $model->$name, (boolean) $model->$name, array('label' => $model->get_label($name)));
  }

  /**
   * Checkbox generation.
   **/
  public static function checkbox($name, $value = NULL, $checked = false, array $attributes = NULL)
  {
    $template = new View_Form_Checkbox;
    $template->name = $name;
    $template->label = __(Arr::get($attributes, 'label'));
    $template->is_selected = (boolean) $checked;
    $template->value = (boolean) $value;
    return self::render_control($template);
  }

  public static function select($name, array $options = NULL, $selected = NULL, array $attributes = NULL)
  {
    $template = new View_Form_Select;
    $template->name = $name;
    $template->label = __(Arr::get($attributes, 'label'));
    $template->options = array();
    foreach ($options as $name => $value)
    {
      $template->options[] = array(
        'name' => $name,
        'value' => $value,
        'is_selected' => ($name == $selected)
      );
    }
    return self::render_control($template);
  }

  public static function btn($name, $text = 'Send', $btclass = NULL, $type = NULL, array $parameters = NULL)
  {
    if (empty($btclass))
    {
      $btclass = 'default';
    }
    if (empty($type))
    {
      $type = 'button';
    }
    $class = 'btn btn-'.$btclass.' '.Arr::get($parameters, 'class');
    if (is_null($parameters))
    {
      $parameters = array();
    }
    $parameters['class'] = $class;
    $parameters['type'] = $type;
    return parent::button($name, __($text), $parameters);
  }

  public static function btn_submit($text = 'Send', $is_default = TRUE)
  {
    return self::btn('submit', __($text), 'primary', 'submit');
  }
  
  protected static function render_control($template)
  {
    $renderer = Kostache_Layout::factory('form/control');
    return $renderer->render($template, $template->_view);
  }
  protected static function render_inline_control($template)
  {
    $renderer = Kostache_Layout::factory('form/inline/control');
    return $renderer->render($template, $template->_view);
  }

  public static function ajax_submit($name, $value)
  {
    return self::btn($name, $value, NULL, NULL, array('onclick' => "ajax_submit('".$name."')"));
  }

}
