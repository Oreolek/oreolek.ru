<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Common control layout view
 **/
class View_Form_Control {
  public $name; //field name
  public $value; //default value
  public $label; //field label
  public $_view; //template to use
  /**
   * Field ID. Convention is '<field type>-<field name>'.
   * @retval string ID
   **/
  public function id()
  {
    return $name;
  }
}
