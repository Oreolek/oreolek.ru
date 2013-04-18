<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Page extends Controller_Template
{
  public $template = 'page/view';
  /**
   * View a page.
   **/
  public function action_view()
  {
    $this->template = new View('page/view');
    $id = $this->request->param('id');
    $page = ORM::factory('Page', $id);
    if (!$page->loaded()) $this->redirect('error/404');
    $this->template->title = $page->name;
    $this->template->content = Markdown::instance()->transform($page->content);
  }
  public function action_index()
  {
    $this->template = new View('page/index');
    $this->template->title = 'Содержание';
    $this->template->pages = ORM::factory('Page')->order_by('posted_at', 'DESC')->find_all(); 
  }
  /**
   * Create a page (for admin)
   * @todo check for admin privileges
   **/
  public function action_create()
  {
    $this->template = new View('page/create');
    $this->template->title = 'Новая страница';
    $page = ORM::factory('Page');
    if (HTTP_Request::POST == $this->request->method()) {
      $page->content = $this->request->post('content');
      $page->name = $this->request->post('title');
      if ($page->check()) {
        $page->create();
        $this->redirect('page/view/' . $page->id);
      }
    }
  }
}
