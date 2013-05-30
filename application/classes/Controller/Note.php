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
    $this->template = new View('index');
    $this->template->header = Request::factory('header/standard')->post('title','Заметки')->execute();
    $this->template->is_admin = TRUE;
    $this->template->items = ORM::factory('Note')
      ->order_by('posted_at', 'DESC')
      ->find_all(); 
    $this->template->footer = Request::factory('footer/standard')->execute(); 
  }


  /**
   * View a note.
   **/
  public function action_view()
  {
    $this->template = new View('note/view');
    $id = $this->request->param('id');
    $note = ORM::factory('Note', $id);
    if (!$note->loaded())
    {
      $this->redirect('error/404');
    }
    $title = $note->name;
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->content = Markdown::instance()->transform($note->content);
    $this->template->footer = Request::factory('footer/standard')->execute(); 
  }

  public function action_edit()
  {
    $this->template = new View('note/edit');
    $title = 'Редактирование заметки';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->footer = Request::factory('footer/standard')->execute(); 
    $note = ORM::factory('Note', $this->request->param('id'));
    if (!$note->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template->errors = array();

    if (HTTP_Request::POST == $this->request->method()) {
      $note->content = $this->request->post('content');
      $note->name = $this->request->post('name');
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
    $this->template->note = $note;
  }

  public function action_delete()
  {
    $this->template = new View('post/delete');
    $id = $this->request->param('id');
    $note = ORM::factory('Note', $id);
    if (!$note->loaded())
    {
      $this->redirect('error/404');
    }
    $title = 'Удаление записки';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->title = $note->name;
    $this->template->content = Markdown::instance()->transform($note->content);
    $this->template->footer = Request::factory('footer/standard')->execute(); 

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
    $this->template = new View('note/edit');
    $title = 'Новая записка';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->errors = array();
    $note = ORM::factory('Note');
    if (HTTP_Request::POST == $this->request->method()) {
      $note->content = $this->request->post('content');
      $note->name = $this->request->post('name');
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
    $this->template->note = $note;
    $this->template->footer = Request::factory('footer/standard')->execute(); 
  }
}
