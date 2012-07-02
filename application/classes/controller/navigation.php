<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Navigation extends Controller_Template {
 public $template = 'navigation/actions';
 public function action_actions() {
  $this->template = new View('navigation/actions');
  $login_or_logout = HTML::anchor('login', 'Вход');
  $this->template->login_or_logout = $login_or_logout;
  if (Auth::instance()->logged_in()){
   $this->template->login_or_logout = HTML::anchor('logout', 'Выход');
  }
  if (Auth::instance()->logged_in('admin')){
   $this->template->admin_actions = View::factory('navigation/admin')->render();
  }
 }
 public function action_view(){$this->request->redirect('');}
}
