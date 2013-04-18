<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Post extends Controller_Template {
  public $template = 'post/view';
  /**
   * View a post.
   **/
  public function action_view()
  {
    $this->template = new View('post/view');
    $id = $this->request->param('id');
    $post = ORM::factory('Post', $id);
    if (!$post->loaded()) $this->redirect('error/404');
    $this->template->title = $post->name;
    $this->template->id = $post->id;
    $this->template->content = Markdown::instance()->transform($post->content);
  }

  public function action_index()
  {
    $this->template = new View('post/index');
    $this->template->title = 'Содержание';
    $this->template->posts = ORM::factory('Post')->order_by('posted_at', 'DESC')->find_all(); 
  }

  public function action_fresh()
  {
    $this->template = new View('post/index');
    $this->template->title = 'Cвежие записи';
    $this->template->posts = ORM::factory('Post')->order_by('posted_at', 'DESC')->limit(10)->find_all(); 
  }

  /**
   * Create a post (for admin)
   * @todo check for admin privileges
   **/
  public function action_create()
  {
    $this->template = new View('post/create');
    $this->template->title = 'Новая запись';
    $post = ORM::factory('Post');
    if (HTTP_Request::POST == $this->request->method()) {
      $post->content = $this->request->post('content');
      $post->name = $this->request->post('name');
      if ($post->check()) {
        $post->create();
        $this->redirect('post/view/' . $post->id);
      }
    }
    $this->template->post = $post;
  }
}
