<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Tag controller.
 * Tags
 **/
class Controller_Tag extends Controller_Layout {
  public $template = 'tag/view';
  protected $secure_actions = array(
		'edit' => array('login', 'admin'),
		'create' => array('login', 'admin'),
    'delete' => array('login', 'admin')
  );
  public function action_view()
  {
    $id = $this->request->param('id');
    $tag = ORM::factory('Tag',$id);
    if (!$tag->loaded()) $this->redirect('error/404');
    $title = 'Тег: '.$tag->name;
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->posts = $tag->posts->find_all();
    $this->template->content = Markdown::instance()->transform($tag->description);
    $this->template->footer = Request::factory('footer/standard')->execute(); 
  }

  public function action_edit()
  {
    $id = $this->request->param('id');
    $tag = ORM::factory('Tag',$id);
    if (!$tag->loaded()) $this->redirect('error/404');
    $this->template = new View('tag/edit');
    $title = 'Редактирование тега: '.$tag->name;
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->errors = array();
    $this->template->footer = Request::factory('footer/standard')->execute(); 
    if (HTTP_Request::POST == $this->request->method()) {
      $tag->description = $this->request->post('description');
      $tag->name = $this->request->post('name');
      try {
        if ($tag->check()) $tag->update();
      }
      catch (ORM_Validation_Exception $e)
      {
        $this->template->errors = $e->errors();
      }
      if (empty($this->template->errors)) $this->redirect('tag/view/' . $tag->id);
    }
    $this->template->tag = $tag;
  }

  public function action_create()
  {
    $this->template = new View('tag/edit');
    $tag = ORM::factory('Tag');
    $title = 'Создание тега';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->errors = array();
    $this->template->footer = Request::factory('footer/standard')->execute(); 
    if (HTTP_Request::POST == $this->request->method()) {
      $tag->description = $this->request->post('description');
      $tag->name = $this->request->post('name');
      try {
        if ($tag->check()) $tag->create();
      }
      catch (ORM_Validation_Exception $e)
      {
        $this->template->errors = $e->errors();
      }
      if (empty($this->template->errors)) $this->redirect('tag/view/' . $tag->id);
    }
    $this->template->tag = $tag;
  }
}
