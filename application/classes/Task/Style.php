<?php defined('SYSPATH') OR die('No direct script access.');
 /**
  * This is an automated task to compile and minify SCSS.
  * It has no configurable options.
  * @category Helpers
  * @author Oreolek
  * @license AGPL
  **/
class Task_Style extends Minion_Task
{
  protected function _execute()
  {
    system('scss '.APPPATH.'scss/main.scss ./public/style.css');
  }
}
