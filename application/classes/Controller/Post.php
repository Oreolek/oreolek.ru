<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Post extends Controller_Layout {
  public $template = 'post/view';
  protected $secure_actions = array(
    'create' => array('login','admin'),
		'edit' => array('login','admin'),
	  'delete' => array('login','admin')
  );
  /**
   * View a post.
   **/
  public function action_view()
  {
    $this->template = new View('post/view');
    $id = $this->request->param('id');
    $post = ORM::factory('Post', $id);
    if (!$post->loaded()) $this->redirect('error/404');
    $this->template->header = Request::factory('header/standard')->post('title',$post->name)->execute();
    $this->template->comments = Request::factory('comment/view/' . $id)->execute();
    $this->template->create_comment = Request::factory('comment/create/' . $id)->execute();
    $this->template->content = Markdown::instance()->transform($post->content);
    $this->template->footer = Request::factory('footer/standard')->execute(); 
  }

  public function action_index()
  {
    $this->template = new View('post/index');
    $this->template->header = Request::factory('header/standard')->post('title','Содержание')->execute();
    $this->template->posts = ORM::factory('Post')->order_by('posted_at', 'DESC')->find_all(); 
    $this->template->footer = Request::factory('footer/standard')->execute(); 
  }

  /**
   * 10 fresh posts
   **/
  public function action_fresh()
  {
    $this->template = new View('post/index');
    $title = 'Cвежие записи';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->posts = ORM::factory('Post')->order_by('posted_at', 'DESC')->limit(10)->find_all(); 
    $this->template->footer = Request::factory('footer/standard')->execute(); 
  }

  /**
   * Atom feed for fresh posts
   **/
  public function action_feed()
  {
    $this->auto_render = false;
    $posts = ORM::factory('Post')->order_by('posted_at', 'DESC')->limit(10)->find_all(); 
    $info = array(
      'title' => Kohana::$config->load('common.title'),
      'author' => Kohana::$config->load('common.author'),
      'pubDate' => $posts[0]->posted_at,
    );
    $items = array();
    foreach ($posts as $post)
    {
      array_push($items, array(
        'title' => $post->name,
        'description' => Markdown::instance()->transform($post->content),
        'link' => 'post/view/' . $post->id,
      ));
    }
    $this->response->body( Feed::create($info, $items) );
  }

  /**
   * Create a post (for admin)
   * @todo check for admin privileges
   **/
  public function action_create()
  {
    $this->template = new View('post/create');
    $title = 'Новая запись';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
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
    $this->template->footer = Request::factory('footer/standard')->execute(); 
  }
}
