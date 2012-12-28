<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Footer extends Controller_Template {
 public $template = 'footer';
 public function action_standard() { }
 public function action_view(){$this->redirect('');}
}
