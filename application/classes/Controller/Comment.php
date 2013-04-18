<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Comment extends Controller_Template {
  public $template = 'comment/view';
  /**
   * Show a comments thread by post ID
   **/
  public function action_view()
  {
    $this->template = new View('comment/view');
    $post_id = $this->request->param('post_id');
    $this->template->comments = ORM::factory('Comment')->where('post_id', '=', $post_id)->order_by('posted_at', 'DESC')->find_all(); 
  }
}
