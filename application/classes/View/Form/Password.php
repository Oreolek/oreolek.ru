<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form password input control.
 **/
class View_Form_Password extends View_Form_Control {
  public function id()
  {
    return 'password-'.$this->name;
  }
}
