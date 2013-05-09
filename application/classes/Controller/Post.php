<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Post extends Controller_Layout {
  public $template = 'post/view';
  protected $secure_actions = array(
    'drafts' => array('login','admin'),
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
    if ($post->is_draft == true AND !Auth::instance()->logged_in('admin')) $this->redirect('error/403');
    $title = $post->name;
    if ($post->is_draft) $title .= ' (черновик)';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->comments = Request::factory('comment/view/' . $id)->execute();
    $this->template->create_comment = Request::factory('comment/create/' . $id)->execute();
    $this->template->content = Markdown::instance()->transform($post->content);
    $this->template->footer = Request::factory('footer/standard')->execute(); 
  }

  public function action_index()
  {
    $this->template = new View('post/index');
    $this->template->header = Request::factory('header/standard')->post('title','Содержание')->execute();
    $this->template->is_admin = Auth::instance()->logged_in('admin');
    $this->template->posts = ORM::factory('Post')
      ->where('is_draft', '=', '0')
      ->order_by('posted_at', 'DESC')
      ->find_all(); 
    $this->template->footer = Request::factory('footer/standard')->execute(); 
  }

  /**
   * 10 fresh posts
   **/
  public function action_fresh()
  {
    $this->template = new View('post/index');
    $this->template->is_admin = Auth::instance()->logged_in('admin');
    $title = 'Cвежие записи';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->posts = ORM::factory('Post')
      ->where('is_draft', '=', '0')
      ->order_by('posted_at', 'DESC')
      ->limit(10)
      ->find_all(); 
    $this->template->footer = Request::factory('footer/standard')->execute(); 
  }

  /**
   * Atom feed for fresh posts
   **/
  public function action_feed()
  {
    $this->auto_render = false;
    $posts = ORM::factory('Post')
      ->where('is_draft', '=', '0')
      ->order_by('posted_at', 'DESC')
      ->limit(10)
      ->find_all(); 
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

  public function action_delete()
  {
    $this->template = new View('post/delete');
    $id = $this->request->param('id');
    $post = ORM::factory('Post', $id);
    if (!$post->loaded()) $this->redirect('error/404');
    $title = 'Удаление записи дневника';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->title = $post->name;
    $this->template->content = Markdown::instance()->transform($post->content);
    $this->template->footer = Request::factory('footer/standard')->execute(); 

    $confirmation = $this->request->post('confirmation');
    if ($confirmation === 'yes') {
      $post->delete();
      $this->redirect('page/index');
    }
  }

  /**
   * Create a post (for admin)
   **/
  public function action_create()
  {
    $this->template = new View('post/create');
    $title = 'Новая запись';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->errors = array();
    $post = ORM::factory('Post');
    if (HTTP_Request::POST == $this->request->method()) {
      $post->content = $this->request->post('content');
      $post->name = $this->request->post('name');
      $post->is_draft = $this->request->post('is_draft');
      try {
        if ($post->check()) $post->create();
      }
      catch (ORM_Validation_Exception $e)
      {
        $this->template->errors = $e->errors();
      }
      if (empty($this->template->errors)) $this->redirect('post/view/' . $post->id);
    }
    $this->template->post = $post;
    $this->template->footer = Request::factory('footer/standard')->execute(); 
  }

  /**
   * Draft index
   **/
  public function action_drafts()
  {
    $this->template = new View('post/index');
    $title = 'Содержание дневника (черновики)';
    $this->template->header = Request::factory('header/standard')->post('title',$title)->execute();
    $this->template->is_admin = true; //this action is restricted to admin
    $this->template->posts = ORM::factory('Post')
      ->where('is_draft', '=', '1')
      ->order_by('posted_at', 'DESC')
      ->find_all(); 
    $this->template->footer = Request::factory('footer/standard')->execute(); 
  }
}
