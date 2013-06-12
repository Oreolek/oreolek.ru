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
    $this->template->items = ORM::factory('Comment')->order_by('posted_at', 'DESC')->find_all();
  }
}
