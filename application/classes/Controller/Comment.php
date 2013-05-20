<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Comment extends Controller_Template {
  public $template = 'comment/view';
  /**
   * Show a comments thread by post ID
   **/
  public function action_view()
  {
    $this->template = new View('comment/view');
    $post_id = $this->request->param('id');
    if (is_null($post_id)) throw Kohana_HTTP_Exception::factory(500, 'No post ID specified');
    $this->template->comments = ORM::factory('Comment')
      ->where('post_id', '=', $post_id)
      ->where('is_approved', '=', Model_Comment::STATUS_APPROVED)
      ->order_by('posted_at', 'DESC')
      ->find_all();
  }

  /**
   * Create a comment.
   * @todo fix post_id = NULL
   **/
  public function action_create()
  {
    $this->template = new View('comment/create');
    $post_id = $this->request->param('id');
    $comment = ORM::factory('Comment');
    if (is_null($post_id)) throw Kohana_HTTP_Exception::factory(500, 'No post ID specified');
    $comment->post_id = $post_id;
    if (HTTP_Request::POST == $this->request->method()) {
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
    $this->template->comment = $comment;
  }
}
