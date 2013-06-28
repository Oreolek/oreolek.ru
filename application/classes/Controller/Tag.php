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
    $this->template = new View_Tag_View;
    $this->template->title = 'Тег: '.$tag->name;
    $this->template->show_date = TRUE;
    $this->template->show_create = FALSE;
    $this->template->items = $tag->posts->where('is_draft', '=', '0')->find_all();
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
    $this->template->items = $tag->posts->where('is_draft', '=', '0')->find_all();
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

  /**
   * Atom feed for fresh posts in tag
   **/
  public function action_feed()
  {
    $this->auto_render = false;
    $id = $this->request->param('id');
    $tag = ORM::factory('Tag',$id);
    if (!$tag->loaded())
    {
      $this->redirect('error/404');
    }
    $posts = $tag->posts
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

}
