<?php defined('SYSPATH') OR die('No direct script access.');

class ORM extends Kohana_ORM {
  /**
   * Gets label for field from _labels array
   * @retval string
   **/
  public function get_label($field)
  {
    return Arr::get($this->_labels, $field);
  }
}