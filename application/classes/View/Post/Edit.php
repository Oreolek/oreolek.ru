<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Edit post view controller
 **/
class View_Post_Edit extends View_Edit {
  public $_view = 'edit';
  public $tags;
  public function get_controls()
  {
    $output = '';
    foreach ($this->controls as $key => $value)
    {
      $output .= Form::orm_input($this->model, $key, $value);
    }
    $output .= '<div class="container">'.Form::label('tags', 'Теги').Form::input('tags',$this->get_tags()).'</div>';
    $output .= Form::submit('submit','Отправить');
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
