<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form date input control.
 **/
class View_Form_Date extends View_Form_Control {
  public $maxlength;
  public function id()
  {
    return 'date-'.$this->name;
  }
}
