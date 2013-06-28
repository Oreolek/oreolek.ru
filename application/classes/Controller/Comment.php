<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Comment extends Controller_Layout {
  protected $secure_actions = array(
    'index' => array('login','admin'),
  );

  /**
   * Create a comment.
   **/
  public function action_create()
  {
    $this->auto_render = FALSE;
    $post_id = $this->request->param('id');
    $comment = ORM::factory('Comment');
    if (is_null($post_id))
    {
      throw new HTTP_Exception_500('Не указан ID записи');
    }
    if (HTTP_Request::POST != $this->request->method()) {
      throw new HTTP_Exception_500('Только запросы POST');
    }
    $comment->post_id = $post_id;
    $comment->content = $this->request->post('content');
    $comment->author_name = $this->request->post('author_name');
    $comment->author_email = $this->request->post('author_email');
    $email = $this->request->post('email');
    if (empty($email) AND $comment->check()) {
      if (Kohana::$config->load('common.comment_approval'))
      {
        if (!$comment->antispam_check(Request::user_agent('browser')))
        {
          $comment->is_approved = Model_Comment::STATUS_PENDING;
        }
        else
        {
          $comment->is_approved = Model_Comment::STATUS_APPROVED;
        }
      }
      else
      {
        $comment->is_approved = Model_Comment::STATUS_APPROVED;
      }
      $comment->create();
      $this->redirect('post/view/' . $post_id);
    }
    unset($email);
  }

  public function action_index()
  {
    $this->template = new View_Comment_Index;
    $this->template->title = 'Комментарии дневника';
    $this->template->scripts = array('http://yandex.st/jquery/2.0.2/jquery.min.js','comment_buttons.js');
    $this->template->items = ORM::factory('Comment')->order_by('posted_at', 'DESC')->find_all();
  }

  public function action_edit()
  {
    $id = $this->request->param('id');
    $model = ORM::factory('Comment', $id);
    if (!$model->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template = new View_Edit;
    $this->template->errors = array();
    $this->template->title = 'Редактирование комментария';
    $this->template->controls = array(
      'author_name' => 'input',
      'author_email' => 'input',
      'content' => 'text',
      'is_approved' => 'checkbox'
    );
    if ($this->request->method() === HTTP_Request::POST) {
      $model->values($this->request->post());
      // AJAX JSON checks
      $is_approved = $this->request->post('is_approved');
      if ($is_approved === 'true')
      {
        $model->is_approved = TRUE;
      }
      if ($is_approved === 'false')
      {
        $model->is_approved = FALSE;
      }
      $validation = $model->validate_create($this->request->post());
      try
      {
        if ($model->check())
        {
          $model->update();
        }
        else
        {
          $this->template->errors = $validation->errors('default');
        }
      }
      catch (ORM_Validation_Exception $e)
      {
        $this->template->errors = $e->errors('default');
      }
      if (empty($this->template->errors) AND !$this->request->is_ajax())
      {
        $this->redirect('post/view/' . $model->post);
      }
    }
    $this->template->model = $model;
  }

  public function action_delete()
  {
    $id = $this->request->param('id');
    $model = ORM::factory('Comment', $id);
    if (!$model->loaded())
    {
      $this->redirect('error/404');
    }
    $this->template = new View_Delete;
    $this->template->title = 'Удаление комментария';
    $this->template->content_title = 'Комментарий от '.$comment->author_name;
    $this->template->content = Markdown::instance()->transform($model->content);

    $confirmation = $this->request->post('confirmation');
    if ($confirmation === 'yes') {
      $model->delete();
      $this->redirect('comment/index');
    }

  }

  /**
   * RSS feed for fresh comments
   **/
  public function action_feed()
  {
    $this->auto_render = false;
    $comments = ORM::factory('Comment')
      ->where('is_approved', '=', '1')
      ->order_by('posted_at', 'DESC')
      ->limit(10)
      ->find_all(); 
    $info = array(
        'title' => Kohana::$config->load('common.title').' (комментарии)',
        'pubDate' => strtotime($comments[0]->posted_at),
        'description' => ''
    );
    $items = array();
    foreach ($comments as $comment)
    {
      array_push($items, array(
            'title' => $comment->author_name,
            'description' => Markdown::instance()->transform($comment->content),
            'author' => $comment->author_email,
            'link' => Route::url('default', array('controller' => 'Post', 'action' => 'view', 'id' => $comment->post_id)).'#comment_'.$comment->id,
            'guid' => Route::url('default', array('controller' => 'Post', 'action' => 'view', 'id' => $comment->post_id)).'#comment_'.$comment->id,
      ));
    }
    $this->response->headers('Content-type', 'application/rss+xml');
    $this->response->body( Feed::create($info, $items) );

  }

}
