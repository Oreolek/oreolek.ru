<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Tag controller.
 * Tags are case-sesitive.
 **/
class Controller_Tag extends Controller_Layout {
  public $template = 'tag/view';
  protected $secure_actions = array(
		'edit' => array('login', 'admin'),
		'create' => array('login', 'admin'),
    'delete' => array('login', 'admin')
  );

  /**
   * Index all posts with this tag.
   **/
  public function action_view()
  {
    $id = $this->request->param('id');
    $tag = ORM::factory('Tag',$id);
    if (!$tag->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template = new View_Index;
    $this->template->title = 'Тег: '.$tag->name;
    $this->template->show_date = TRUE;
    $this->template->show_create = FALSE;
    $this->template->items = $tag->posts->find_all();
    $this->template->content = Markdown::instance()->transform($tag->description);
  }

  /**
   * Read all posts in tag.
   **/
  public function action_read()
  {
    $id = $this->request->param('id');
    $tag = ORM::factory('Tag',$id);
    if (!$tag->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template = new View_Read;
    $this->template->title = 'Записи по тегу: '.$tag->name;
    $this->template->items = $tag->posts->find_all();
    $this->template->content = Markdown::instance()->transform($tag->description);
  }

  /**
   * Index all tags.
   **/
  public function action_index()
  {
    $this->template = new View_Index;
    $this->template->title = 'Список меток';
    $this->template->show_date = FALSE;
    $this->template->items = ORM::factory('Tag')->order_by('name', 'ASC')->find_all();
  }

  public function action_edit()
  {
    $id = $this->request->param('id');
    $tag = ORM::factory('Tag',$id);
    if (!$tag->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template = new View_Edit;
    $this->template->title = 'Редактирование тега: '.$tag->name;
    $this->template->errors = array();
    $this->template->controls = array(
      'name' => 'input',
      'description' => 'text',
    );
    if (HTTP_Request::POST == $this->request->method()) {
      $tag->description = $this->request->post('description');
      $tag->name = $this->request->post('name');
      try {
        if ($tag->check())
        {
          $tag->update();
        }
      }
      catch (ORM_Validation_Exception $e)
      {
        $this->template->errors = $e->errors();
      }
      if (empty($this->template->errors))
      {
        $this->redirect('tag/view/' . $tag->id);
      }
    }
    $this->template->model = $tag;
  }

  public function action_create()
  {
    $this->template = new View_Edit;
    $tag = ORM::factory('Tag');
    $this->template->title = 'Создание тега';
    $this->template->errors = array();
    $this->template->controls = array(
      'name' => 'input',
      'description' => 'text',
    );
    if ($this->request->method() === HTTP_Request::POST) {
      $tag->description = $this->request->post('description');
      $tag->name = $this->request->post('name');
      try {
        if ($tag->check())
        {
          $tag->create();
        }
      }
      catch (ORM_Validation_Exception $e)
      {
        $this->template->errors = $e->errors();
      }
      if (empty($this->template->errors))
      {
        $this->redirect('tag/view/' . $tag->id);
      }
    }
    $this->template->model = $tag;
  }

  public function action_delete()
  {
    $this->template = new View_Delete;
    $id = $this->request->param('id');
    $tag = ORM::factory('Tag', $id);
    if (!$tag->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template->title = 'Удаление метки дневника';
    $this->template->content_title = $tag->name.' (записей: '.$tag->posts->count_all().')';
    $this->template->content = Markdown::instance()->transform($tag->description);

    $confirmation = $this->request->post('confirmation');
    if ($confirmation === 'yes') {
      $tag->delete();
      $this->redirect('tag/index');
    }
  }
}
