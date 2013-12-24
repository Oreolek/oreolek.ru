<?php defined('SYSPATH') or die('No direct script access.');

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
    return Form::orm_input($this->model, 'password', 'password');
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
