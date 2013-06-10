<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Comment extends Controller {
  public $auto_render = FALSE;

  /**
   * Create a comment.
   **/
  public function action_create()
  {
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
        $comment->is_approved = Model_Comment::STATUS_PENDING;
      }
      else
      {
        $comment->is_approved = Model_Comment::STATUS_APPROVED;
      }
      //spam check
      $comment->create();
      $this->redirect('post/view/' . $post_id);
    }
    unset($email);
  }
}
