<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Login extends Controller_Template {
 public $template = 'login';
 public function action_view() {
  if(Auth::instance()->logged_in()) return $this->redirect('');
  if ($_POST){
   $user = ORM::factory('user');
   $status = Auth::instance()->login($this->request->post('login'), $this->request->post('password'));
   if ($status) return $this->request->redirect('');
   else $this->template->error = "Неверный логин или пароль.";
  }
 }
}
