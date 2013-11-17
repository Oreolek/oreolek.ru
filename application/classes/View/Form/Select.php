<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form select input control.
 **/
class View_Form_Select extends View_Form_Control {
  public function id()
  {
    return 'select-'.$this->name;
  }
}
