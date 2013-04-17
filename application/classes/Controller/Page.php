<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Page extends Controller_Template
{
  public $template = 'page/view';
  /**
   * View a page.
   * @todo if page not found redirect to 404
   * @todo page model
   **/
  public function action_view()
  {
    $this->template = new View('page/view');
    $id = $this->request->param('id');
    $page = ORM::factory('Page', $id);
    if (!$page->loaded()) $this->redirect('error/404');
    $this->template->title = $page->id;
    $this->template->content = $page->content;
    if ($page->is_markdown) $this->template->content = Markdown::instance()->transform($this->template->content);
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
