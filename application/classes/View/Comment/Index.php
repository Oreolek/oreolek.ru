<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Comment panel view controller.
 **/
class View_Comment_Index extends View_Index {
  /**
   * An internal function to prepare item data.
   **/
  protected function show_item($item)
  {
    $output = array(
        'date' => '',
        'author_email' => '',
        'author_name' => '',
        'content' => '',
        'is_approved' => '',
    );
    $output['date'] = $item->posted_at;
    $output['author_email'] = $item->author_email;
    $output['author_name'] = $item->author_name;
    $output['content'] = Markdown::instance()->transform($item->content);
    $output['post_link'] = Route::url('default', array('controller' => 'Post', 'action' => 'view', 'id' => $item->post));
    $output['comment_id'] = $item->id;
    $output['is_approved'] = Form::checkbox('is_approved', $item->id, (boolean) $item->is_approved, array('disabled' => 'disabled'));
    return $output;
  }


}
