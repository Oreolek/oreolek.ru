<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Page creation/editing view controller
 **/
class View_Page_Edit extends View_Edit {
  public function input_name()
  {
    return Form::orm_input($this->model, 'name');
  }

  public function input_is_draft()
  {
    return Form::orm_input($this->model, 'is_draft', 'checkbox');
  }

  public function input_content()
  {
    return Form::orm_input($this->model, 'content', 'textarea');
  }
}
