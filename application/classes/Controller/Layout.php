<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Layout extends Controller_Template {
  protected $secure_actions = FALSE;
  public function before()
  {
    parent::before();
    $action_name = $this->request->action();
    if (
      is_array($this->secure_actions) &&
      array_key_exists($action_name, $this->secure_actions) && 
      Auth::instance()->logged_in($this->secure_actions[$action_name]) === FALSE
    )
    {
      if (Auth::instance()->logged_in())
      {
        $this->redirect('error/403');
      }
      else
      {
        $this->redirect('user/signin');
      }
    }
    if ($this->auto_render)
    {
      $this->template->footer = Request::factory('footer/standard')->execute();
    }
  }
}
