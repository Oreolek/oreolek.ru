<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Page extends Controller_Layout {
  protected $secure_actions = array(
    'drafts' => array('login','admin'),
    'create' => array('login','admin'),
		'edit' => array('login','admin'),
	  'delete' => array('login','admin')
  );
  /**
   * View a page.
   **/
  public function action_view()
  {
    $this->template = new View_Page_View;
    $id = $this->request->param('id');
    $page = ORM::factory('Page', $id);
    if (!$page->loaded())
    {
      $this->redirect('error/404');
    }
    if ($page->is_draft == true AND !Auth::instance()->logged_in('admin'))
    {
      $this->redirect('error/403');
    }
    if ($page->is_draft) $this->template->title .= ' (черновик)';
    $this->template->content = Markdown::instance()->transform($page->content);
  }
  /**
   * Page index
   **/
  public function action_index()
  {
    $this->template = new View_Index;
    $this->template->title = 'Содержание';
    $this->template->show_date = FALSE;
    $this->template->items = ORM::factory('Page')
      ->where('is_draft', '=', '0')
      ->order_by('name', 'ASC')
      ->find_all(); 
  }
  public function action_delete()
  {
    $this->template = new View_Delete;
    $id = $this->request->param('id');
    $page = ORM::factory('Page', $id);
    if (!$page->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template->title = 'Удаление страницы';
    $this->template->content_title = $page->name;
    $this->template->content = Markdown::instance()->transform($page->content);

    $confirmation = $this->request->post('confirmation');
    if ($confirmation === 'yes') {
      $page->delete();
      $this->redirect('page/index');
    }
  }
  /**
   * Create a page (for admin)
   **/
  public function action_create()
  {
    $this->template = new View_Edit;
    $this->template->title = 'Новая страница';
    $this->template->errors = array();
    $page = ORM::factory('Page');
    $this->template->controls = array(
      'name' => 'input',
      'content' => 'text',
      'is_draft' => 'checkbox',
    );
    if ($this->request->method() === HTTP_Request::POST) {
      $page->content = $this->request->post('content');
      $page->name = $this->request->post('name');
      $page->is_draft = $this->request->post('is_draft');
      try {
        if ($page->check())
        {
          $page->create();
        }
      }
      catch (ORM_Validation_Exception $e)
      {
        $this->template->errors = $e->errors('');
      }
      if (empty($this->template->errors))
      {
        $this->redirect('page/view/' . $page->id);
      }
    }
    $this->template->model = $page;
  }
  /**
   * Edit a page (for admin)
   **/
  public function action_edit()
  {
    $this->template = new View_Edit;
    $this->template->title = 'Редактирование страницы';
    $id = $this->request->param('id');
    $page = ORM::factory('Page', $id);
    if (!$page->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template->errors = array();
    $this->template->controls = array(
      'name' => 'input',
      'content' => 'text',
      'is_draft' => 'checkbox',
    );
    if ($this->request->method() === HTTP_Request::POST) {
      $page->content = $this->request->post('content');
      $page->name = $this->request->post('name');
      $page->is_draft = $this->request->post('is_draft');
      try {
        if ($page->check())
        {
          $page->update();
        }
      }
      catch (ORM_Validation_Exception $e)
      {
        $this->template->errors = $e->errors('');
      }
      if (empty($this->template->errors))
      {
        $this->redirect('page/view/' . $page->id);
      }
    }
    $this->template->model = $page;
  }

  /**
   * Draft index
   **/
  public function action_drafts()
  {
    $this->template = new View_Index;
    $this->template->title = 'Содержание (черновики)';
    $this->template->show_date = FALSE;
    $this->template->items = ORM::factory('Page')
      ->where('is_draft', '=', '1')
      ->order_by('name', 'DESC')
      ->find_all(); 
  }
}
