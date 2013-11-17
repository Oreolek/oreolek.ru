<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form text input control.
 **/
class View_Form_Input extends View_Form_Control {
  public $maxlength;
  public function id()
  {
    return 'input-'.$this->name;
  }
}
