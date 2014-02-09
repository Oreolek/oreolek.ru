<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Note controller.
 * Note is just a piece of text only admin can edit and view.
 **/
class Controller_Note extends Controller_Layout {
  public $template = 'note/view';
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
    $this->template = new View_Note_View;
    $id = $this->request->param('id');
    $note = ORM::factory('Note', $id);
    if (!$note->loaded())
    {
      $this->redirect('error/404');
    }
    $access = FALSE;
    if (!empty($note->password))
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
    $this->template = new View_Edit;
    $this->template->title = 'Редактирование заметки';
    $this->template->controls = array(
      'name' => 'input',
      'password' => 'password',
      'content' => 'text',
    );
    $note = ORM::factory('Note', $this->request->param('id'));
    if (!$note->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template->errors = array();

    if (HTTP_Request::POST == $this->request->method()) {
      $note->values($this->request->post(), array('content', 'name', 'password'));
      try {
        if ($note->check())
        {
          $note->update();
        }
      }
      catch (ORM_Validation_Exception $e)
      {
        $this->template->errors = $e->errors();
      }

      if (empty($this->template->errors))
      {
        $this->redirect('note/view/' . $note->id);
      }
    }
    $this->template->model = $note;
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
    $this->template->controls = array(
      'name' => 'input',
      'password' => 'password',
      'content' => 'text',
    );
    $this->template->errors = array();
    $note = ORM::factory('Note');
    if (HTTP_Request::POST == $this->request->method()) {
      $note->values($this->request->post(), array('content', 'name', 'password'));
      try {
        if ($note->check())
        {
          $note->create();
        }
      }
      catch (ORM_Validation_Exception $e)
      {
        $this->template->errors = $e->errors();
      }
      if (empty($this->template->errors))
      {
        $this->redirect('note/view/'.$note->id);
      }
    }
    $this->template->model = $note;
  }
}
