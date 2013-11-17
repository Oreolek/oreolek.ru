<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Visual editor textarea
 **/
class View_Form_WYSIWYG extends View_Form_Control {
  public function id()
  {
    return 'wysiwyg-'.$this->name;
  }
}
