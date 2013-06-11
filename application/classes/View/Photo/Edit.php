<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Edit photo view controller
 **/
class View_Photo_Edit extends View_Edit {
  public $image_path;

  public function image()
  {
    if($this->image_path)
    {
      return HTML::image($image_path);
    }
  }
}
