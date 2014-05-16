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
 * Note controller.
 * Note is just a piece of text only admin can edit and view.
 **/
class Controller_Note extends Controller_Layout {
  protected $secure_actions = array(
    'index' => array('login','admin'),
    'create' => array('login','admin'),
		'edit' => array('login','admin'),
	  'delete' => array('login','admin')
  );

  /**
   * Note index - no custom template
   **/
  public function action_index()
  {
    $this->template = new View_Index;
    $this->template->title = 'Заметки';
    $this->template->show_date = FALSE;
    $this->template->items = ORM::factory('Note')
      ->order_by('name', 'DESC')
      ->find_all(); 
  }

  /**
   * View a note.
   **/
  public function action_view()
  {
    $this->template = new View_Layout;
    $this->template->_view = 'view';
    $id = $this->request->param('id');
    $note = ORM::factory('Note', $id);
    if (!$note->loaded())
    {
      $this->redirect('error/404');
    }
    $access = FALSE;
    if (!empty($note->password) && !Auth::instance()->logged_in('admin'))
    {
      $password_post = $this->request->post('password');
      if ($password_post)
      {
        Cookie::set('password', $password_post);
      }
      $password = Cookie::get('password', $password_post);
      if ($password != $note->password)
      {
        $this->auto_render = TRUE;
        $this->template = new View_Post_Password;
        $this->template->model = ORM::factory('Note');
        $this->template->controls = array(
          'password' => 'password',
        );
        $this->template->message = 'Эта заметка закрыта паролем. Введите пароль для доступа к тексту и комментариям.';
        if (!empty($password))
        {
          $this->template->message = 'Сохранённый пароль не подходит. Попробуйте ещё раз.';
        }
        return;
      }
      else
      {
        $access = TRUE;
      }
    }
    else
    {
        $access = TRUE;
    }
    if ($access)
    {
      $this->template->title = $note->name;
      $this->template->content = Markdown::instance()->transform($note->content);
    }
  }

  public function action_edit()
  {
    $this->template = new View_Note_Edit;
    $this->template->title = __('Edit note');
    $id = $this->request->param('id');
    $note = ORM::factory('Note', $id);
    if (!$note->loaded())
    {
      $this->redirect('error/404');
    }
    $this->edit($note);
  }

  public function action_delete()
  {
    $this->template = new View_Delete;
    $id = $this->request->param('id');
    $note = ORM::factory('Note', $id);
    if (!$note->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template->title = 'Удаление записки';
    $this->template->content_title = $note->name;
    $this->template->content = Markdown::instance()->transform($note->content);

    $confirmation = $this->request->post('confirmation');
    if ($confirmation === 'yes') {
      $note->delete();
      $this->redirect('note/index');
    }
  }

  /**
   * Create a note
   **/
  public function action_create()
  {
    $this->template = new View_Edit;
    $this->template->title = 'Новая записка';
    $note = ORM::factory('Note');
    $this->edit($note);
  }

  protected function edit($note)
  {
    $this->template->controls = array(
      'name' => 'input',
      'password' => 'input',
      'content' => 'text',
    );
    $this->template->errors = array();

    if (HTTP_Request::POST === $this->request->method()) {
      $validation = $note->validate_create($this->request->post());
      $note->values($this->request->post(), array('content', 'name', 'password'));
      if ($this->request->is_ajax())
      {
        $this->auto_render = FALSE;
        $retval = array(
          'preview' => Markdown::instance()->transform($note->content),
        );
        $this->response->body(json_encode($retval));
      }
      if ($validation->check())
      {
        if ($this->request->post('mode') === 'save')
        {
          try
          {
            $note->save();
          }
          catch (ORM_Validation_Exception $e)
          {
            $this->template->errors = $e->errors('note');
          }
        }
      }
      else
      {
        $this->template->errors = $validation->errors('note');
      }

      if ($this->request->is_ajax())
        return;

      if (empty($this->template->errors))
      {
        $this->redirect('note/view/' . $note->id);
      }
    }
    $this->template->model = $note;
  }
}
