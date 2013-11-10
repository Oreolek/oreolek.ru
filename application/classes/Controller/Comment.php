<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Comment extends Controller_Layout {
  protected $secure_actions = array(
    'index' => array('login','admin'),
  );

  /**
   * AJAX action to create a comment.
   **/
  public function action_create()
  {
    $this->auto_render = FALSE;
    $post_id = $this->request->param('id');
    $comment = ORM::factory('Comment');
    if (empty($post_id))
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
    $title = $this->request->post('title');
    $name = $this->request->post('name');
    if (empty($email) AND empty($title) AND empty($name) AND $comment->check()) {
      if (Kohana::$config->load('common')->get('comment_approval'))
      {
        if (
          Model_Comment::antispam_check($comment->content) == FALSE OR
          Model_Comment::useragent_check(Request::user_agent('browser') == FALSE)
        )
        {
          $comment->is_approved = Model_Comment::STATUS_SPAM;
        }
        else
        {
          $comment->is_approved = Model_Comment::STATUS_APPROVED;
        }
      }
      else
      {
        $comment->is_approved = Model_Comment::STATUS_PENDING;
      }
      $comment->create();
      $this->redirect('post/view/' . $post_id);
    }
    unset($email);
  }

  /**
   * AJAX action to get a form for a new comment.
   * TODO cache it
   **/
  public function action_form()
  {
    $this->auto_render = FALSE;
    if ( ! Fragment::load('comment_form', Date::DAY * 7))
    {
      $model = ORM::factory('Comment');
      $inputs = array();
      $inputs['author_email'] = Form::orm_textinput($model, 'author_email');
      $inputs['author_name'] = Form::orm_textinput($model, 'author_name');
      $inputs['content'] = Form::orm_textarea($model, 'content');
      $this->template = new View_Comment_Form;
      $this->template->url = URL::site('comment/create/-ID-');
      $this->template->inputs = $inputs;
      $renderer = Kostache::factory();
      $this->response->body($renderer->render($this->template, $this->template->_view));

      Fragment::save();
    }
  }

  public function action_index()
  {
    $this->template = new View_Comment_Index;
    $this->template->title = 'Комментарии дневника';
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
    $this->template->content_title = 'Комментарий от '.$model->author_name;
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
