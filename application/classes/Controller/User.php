<?php defined('SYSPATH') or die('No direct script access.');
/*
 *  Personal site oreolek.ru source code
 *  Copyright (C) 2014 Alexander Yakovlev
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>. 
 */

/**
 * User controller.
 * Only sign in and edit. The only administration user is created in the install process.
 **/
class Controller_User extends Controller_Layout {
  protected $secure_actions = array(
		'edit' => array('login'),
  );
  public function action_view()
  {
    $this->redirect('');
  }
  public function action_signin()
  {
    if (Auth::instance()->logged_in())
    {
      $this->redirect('post/index');
    }
    // force https; this is the only action to require this special rule
    if (Kohana::$config->load('common')->get('force_https'))
    {          
      if (!$this->request->secure())
      {
        $this->request->secure(TRUE);
      } 
    }
    $this->template = new View_User_Signin;
    $this->template->title = 'Вход в систему';
    $this->template->errors = array();
    $this->template->controls = array(
      'username' => 'input',
      'password' => 'password'
    );
    $user = ORM::factory('User');
    if (HTTP_Request::POST == $this->request->method()) {
      $user->username = $this->request->post('username');
      $validation = Validation::factory($this->request->post())
        ->rules('username', array(
          array('not_empty'),
          array('max_length', array(':value', 32))
        ))
        ->rule('password', 'not_empty');
      if ($validation->check()) {
        if (Auth::instance()->login( $this->request->post('username'), $this->request->post('password'), true))
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
        $this->template->errors = $validation->errors('default');
      }
    }
    $this->template->model = $user;
  }

  /**
   * Set user password - available only in development mode
   **/
  public function action_password()
  {
    $this->template = new View_Edit;
    $this->template->title = 'Изменение пароля';
    $this->template->errors = array();
    $this->template->controls = array(
      'username' => 'input',
      'password' => 'password'
    );
    $user = ORM::factory('User');
    if (HTTP_Request::POST == $this->request->method()) {
      $user = ORM::factory('User')->where('username', '=', $this->request->post('username'))->find();
      if (!$user->loaded())
      {
        $this->template->errors = array('Указанный пользователь не найден. Пожалуйста, проверьте имя пользователя.');
      }
      else
      {
        $validation = Validation::factory($this->request->post())
          ->rule('password', 'not_empty');
        if ($validation->check()) {
          $user->password = $this->request->post('password');
          if ($user->update())
          {
            $this->redirect('user/pwmessage');
          }
        }
        else
        {
          $this->template->errors = $validation->errors('default');
        }
      }
    }
    $this->template->model = $user;
  }

  /**
   * Message when password is successfully changed.
   **/
  public function action_pwmessage()
  {
    $this->template = new View_Message;
    $this->template->title = 'Пароль изменён';
    $this->template->message = 'Пароль пользователя успешно изменён.';
  }

  /**
   * Edit own user information -- change password etc.
   **/
  public function action_edit()
  {
    if (!Auth::instance()->logged_in())
    {
      $this->redirect('post/index');
    }
    $this->template = new View_Edit;
    $this->template->title = 'Редактирование логина и пароля';
    $this->template->errors = array();
    $this->template->controls = array(
      'username' => 'input',
      'password' => 'password'
    );
    $user = Auth::instance()->get_user();
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
    $this->template->model = $user;
  }
}
