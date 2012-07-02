<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Logout extends Controller {
 public function action_view()	{
  if (Auth::instance()->logout()) return $this->request->redirect('login');
	 else	$this->template->error = "Ошибка выхода пользователя.";
 }
}
