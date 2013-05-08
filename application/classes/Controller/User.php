<?php defined('SYSPATH') or die('No direct script access.');

/**
 * User controller.
 * Only sign in and edit. The only administration user is created in the install process.
 **/
class Controller_User extends Controller_Layout {
  public $template = 'signin';
  protected $secure_actions = array(
		'edit' => array('login'),
  );
  public function action_view(){$this->redirect('');}
  public function action_signin()
  {
    if (Auth::instance()->logged_in()) $this->redirect('post/index');
    $this->template->header = Request::factory('header/standard')->post('title', 'Вход на сайт')->execute();
    $this->template->errors = array();
    if (HTTP_Request::POST == $this->request->method()) {
      $validation = Validation::factory($this->request->post())
        ->rules('username', array(
          array('not_empty'),
          array('max_length', array(':value', 32))
        ))
        ->rule('password', 'not_empty');
      if ($validation->check()) {
        if (Auth::instance()->login( $this->request->post('username'), $this->request->post('password')))
        {
          $this->redirect('post/index');
        }
        else
        {
          array_push($this->template->errors, 'Ошибка авторизации. Проверьте правильность имени пользователя и пароля.');
        }
      }
      else
      {
        $this->template->errors = $validation->errors();
      }
    }
    $this->template->user = ORM::factory('User');
  }

  /**
   * Edit own user information -- change password etc.
   **/
  public function action_edit()
  {
    if (!Auth::instance()->logged_in()) $this->redirect('post/index');
    $this->template->header = Request::factory('header/standard')->post('title', 'Редактирование логина и пароля')->execute();
    $user = Auth::instance()->get_user();
    $this->template->errors = array();
    if (HTTP_Request::POST == $this->request->method()) {
      $validation = Validation::factory($this->request->post())
        ->rules('username', array(
          array('not_empty'),
          array('max_length', array(':value', 32))
        ))
        ->rule('password', 'not_empty');
      if ($validation->check()) {
        $user->values($this->request->post());
        $user->update();
        $this->redirect('post/index');
      }
      else
      {
        $this->template->errors = $validation->errors();
      }
    }
    $this->template->user = $user;
  }
}
