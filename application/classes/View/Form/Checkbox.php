<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form checkbox
 **/
class View_Form_Checkbox extends View_Form_Control {
  public $is_selected;
  public $value = 1;
  public function id()
  {
    return 'checkbox-'.$this->name;
  }
}
