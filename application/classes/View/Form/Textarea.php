<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form textarea control.
 **/
class View_Form_Textarea extends View_Form_Control {
  public function id()
  {
    return 'textarea-'.$this->name;
  }
}
