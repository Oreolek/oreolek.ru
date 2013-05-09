<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Page extends Controller_Layout {
  public $template = 'page/view';
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
    $id = $this->request->param('id');
    $page = ORM::factory('Page', $id);
    if (!$page->loaded()) $this->redirect('error/404');
    if ($page->is_draft == true AND !Auth::instance()->logged_in('admin')) $this->redirect('error/403');
    $title = $page->name;
    if ($page->is_draft) $title .= ' (черновик)';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->content = Markdown::instance()->transform($page->content);
  }
  /**
   * Page index
   **/
  public function action_index()
  {
    $this->template = new View('page/index');
    $title = 'Содержание';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->pages = ORM::factory('Page')
      ->where('is_draft', '=', '0')
      ->order_by('posted_at', 'DESC')
      ->find_all(); 
    $this->template->footer = Request::factory('footer/standard')->execute(); 
  }
  public function action_delete()
  {
    $this->template = new View('page/delete');
    $id = $this->request->param('id');
    $page = ORM::factory('Page', $id);
    if (!$page->loaded()) $this->redirect('error/404');
    $title = 'Удаление страницы';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->page_title = $page->name;
    $this->template->page_content = Markdown::instance()->transform($page->content);
    $this->template->footer = Request::factory('footer/standard')->execute(); 

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
    $this->template = new View('page/create');
    $title = 'Новая страница';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $page = ORM::factory('Page');
    if (HTTP_Request::POST == $this->request->method()) {
      $page->content = $this->request->post('content');
      $page->name = $this->request->post('title');
      if ($page->check()) {
        $page->create();
        $this->redirect('page/view/' . $page->id);
      }
    }
    $this->template->page = $page;
    $this->template->footer = Request::factory('footer/standard')->execute(); 
  }
  /**
   * Edit a page (for admin)
   **/
  public function action_edit()
  {
    $this->template = new View('page/edit');
    $title = 'Редактирование страницы';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->footer = Request::factory('footer/standard')->execute(); 
    $id = $this->request->param('id');
    $page = ORM::factory('Page', $id);
    if (!$page->loaded()) $this->redirect('error/404');
    $this->template->page = $page;
    $this->template->page_content = $page->content;
    if (HTTP_Request::POST == $this->request->method()) {
      $page->content = $this->request->post('content');
      $page->name = $this->request->post('name');
      if ($page->check()) {
        $page->update();
        $this->redirect('page/view/' . $page->id);
      }
    }
  }

  /**
   * Draft index
   **/
  public function action_drafts()
  {
    $this->template = new View('page/index');
    $title = 'Содержание (черновики)';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->pages = ORM::factory('Page')
      ->where('is_draft', '=', '1')
      ->order_by('posted_at', 'DESC')
      ->find_all(); 
    $this->template->footer = Request::factory('footer/standard')->execute(); 
  }
}
