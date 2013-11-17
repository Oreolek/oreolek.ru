<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Edit post view controller
 **/
class View_Post_Edit extends View_Edit {
  public $_view = 'edit';
  public $scripts = array(
    'jquery',
    'jquery.autosize-min.js',
    'lightbox-2.6.min.js',
    'autosave.js'
  );
  public $tags;
  public function get_controls()
  {
    $output = '';
    foreach ($this->controls as $key => $value)
    {
      $output .= Form::orm_input($this->model, $key, $value);
    }
    $output .= Form::input('tags', $this->get_tags(), array('label' => 'Теги'));
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
