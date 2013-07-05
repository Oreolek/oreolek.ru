<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Post extends Controller_Layout {
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
    $this->template = new View_Post_View;
    $id = $this->request->param('id');
    $post = ORM::factory('Post', $id);
    if (!$post->loaded()) $this->redirect('error/404');
    if ($post->is_draft == true AND !Auth::instance()->logged_in('admin')) $this->redirect('error/403');
    $this->template->title = $post->name;
    if ($post->is_draft) $this->template->title .= ' (черновик)';
    $this->template->id = $post->id;
    $this->template->tags = $post->tags->find_all();
    $this->template->content = Markdown::instance()->transform($post->content);
    $this->template->date = $post->creation_date();
    $this->template->comments = ORM::factory('Comment')
      ->where('post_id', '=', $post->id)
      ->where('is_approved', '=', Model_Comment::STATUS_APPROVED)
      ->order_by('posted_at', 'ASC')
      ->find_all();
  }

  public function action_edit()
  {
    $this->template = new View_Post_Edit;
    $this->template->title = 'Редактирование записи';
    $id = $this->request->param('id');
    $post = ORM::factory('Post', $id);
    if (!$post->loaded())
    {
      $this->redirect('error/404');
    }
    $this->edit_post($post);
  }

  /**
   * Short index with only post headings
   **/
  public function action_index()
  {
    $this->template = new View_Index;
    $this->template->items = ORM::factory('Post')
      ->where('is_draft', '=', '0')
      ->order_by('posted_at', 'DESC')
      ->find_all();
  }
  
  /**
   * Actually read all posts on one page
   **/
  public function action_read()
  {
    $this->template = new View_Read;
    $this->template->title = 'Дневник';
    $this->template->items = ORM::factory('Post')
      ->with('comments')
      ->where('is_draft', '=', '0')
      ->order_by('posted_at', 'DESC')
      ->find_all();
  }

  /**
   * 10 fresh posts
   **/
  public function action_fresh()
  {
    $this->template = new View_Index;
    $this->template->title = 'Cвежие записи';
    $this->template->items = ORM::factory('Post')
      ->where('is_draft', '=', '0')
      ->order_by('posted_at', 'DESC')
      ->limit(10)
      ->find_all(); 
  }

  /**
   * RSS feed for fresh posts
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
        'pubDate' => strtotime($posts[0]->posted_at),
        'description' => ''
    );
    $items = array();
    foreach ($posts as $post)
    {
      array_push($items, array(
            'title' => $post->name,
            'description' => Markdown::instance()->transform($post->content),
            'author' => Kohana::$config->load('common.author_email').' ('.Kohana::$config->load('common.author').')',
            'link' => Route::url('default', array('controller' => 'Post', 'action' => 'view', 'id' => $post->id)),
            'guid' => Route::url('default', array('controller' => 'Post', 'action' => 'view', 'id' => $post->id)),
      ));
    }
    $this->response->headers('Content-type', 'application/rss+xml');
    $this->response->body( Feed::create($info, $items) );
  }

  public function action_delete()
  {
    $this->template = new View_Delete;
    $id = $this->request->param('id');
    $post = ORM::factory('Post', $id);
    if (!$post->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template->title = 'Удаление записи дневника';
    $this->template->content_title = $post->name;
    $this->template->content = Markdown::instance()->transform($post->content);

    $confirmation = $this->request->post('confirmation');
    if ($confirmation === 'yes') {
      $post->delete();
      $this->redirect('post/index');
    }
  }

  /**
   * Create a post (for admin)
   **/
  public function action_create()
  {
    $this->template = new View_Post_Edit;
    $this->template->title = 'Новая запись';
    $this->template->errors = array();
    $post = ORM::factory('Post');
    $this->edit_post($post);
  }

  /**
   * Draft index
   **/
  public function action_drafts()
  {
    $this->template = new View_Index;
    $this->template->title = 'Содержание дневника (черновики)';
    $this->template->items = ORM::factory('Post')
      ->where('is_draft', '=', '1')
      ->order_by('posted_at', 'DESC')
      ->limit(10)
      ->find_all(); 
  }

  /**
   * Edit or create post.
   * Post model should be initialized with empty post (create) or existing one (update).
   **/
  protected function edit_post($post)
  {
    $this->template->errors = array();
    $this->template->tags = $post->tags->find_all();
    $this->template->controls = array(
      'name' => 'input',
      'content' => 'text',
      'is_draft' => 'checkbox',
      'posted_at' => 'input',
    );
    
    if ($this->request->method() === HTTP_Request::POST) {
      $post->content = $this->request->post('content');
      $post->name = $this->request->post('name');
      $post->is_draft = $this->request->post('is_draft');
      $post->posted_at = $this->request->post('posted_at');
      $tags = $this->request->post('tags');
      $validation = $post->validate_create($this->request->post());
      $mode = 'edit';
      if ($this->request->post('preview') != '')
      {
        $mode = 'view';
      }
      try
      {
        if ($validation->check())
        {
          if ($mode === 'edit')
          {
            if ($post->loaded())
            {
              $post->update();
            }
            else
            {
              $post->create();
            }
          }
        }
        else
        {
          $this->template->errors = $validation->errors('default');
        }
        if ($mode === 'view' and !empty($post->content))
        {
          $this->template->preview = Markdown::instance()->transform($post->content);
        }
      }
      catch (ORM_Validation_Exception $e)
      {
        $this->template->errors = $e->errors('default');
      }
      if (empty($this->template->errors) && $mode === 'edit')
      {
        if (!empty($tags))
        {
          $tags = explode(',', $tags);
          $tags = array_map('trim', $tags);
          //adding new tags
          foreach ($tags as $tag)
          {
            $model = ORM::factory('Tag')->where('name', '=', $tag)->find();
            if (!$model->loaded())
            {
              $model = ORM::factory('Tag');
              $model->name = $tag;
              $model->create();
            }
            if (!$post->has('tags', $model->id))
            {
              $post->add('tags', $model->id);
            }
          }
          
          $tag_models = $post->tags->find_all();
          //deleting unused tags
          foreach ($tag_models as $tag)
          {
            if (array_search($tag->name, $tags) === FALSE)
            {
              $post->remove('tags', $tag->id);
            }
          }
        }
        $this->redirect('post/view/' . $post->id);
      }
    }
    $this->template->model = $post;
  }
}
