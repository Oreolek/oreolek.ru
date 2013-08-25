<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Post view controller.
 **/
class View_Post_View extends View_Layout {
  /**
   * Post tag models.
   **/
  public $tags;
  /**
   * Post ID
   **/
  public $id;
  /**
   * Post comments
   **/
  public $comments;
  public $date;

  public $scripts = array(
    'jquery',
    'jquery.autosize-min.js',
    'lightbox-2.6.min.js'
  );

  public function get_tags()
  {
    $output = 'Теги: ';
    if (count($this->tags) > 0)
    {
      $i = 0;
      foreach ($this->tags as $tag)
      {
        if ($i > 0) $output .= ', ';
        $output .= '<a href="'.URL::site('tag/view/'.$tag->id).'">'.$tag->name.'</a>';
        $i++;
      }
    }
    return $output;
  }

  public function get_comments()
  {
    $result = array();
    foreach ($this->comments as $comment)
    {
      $comment_out = array(
        'content' => Markdown::instance()->transform($comment->content),
        'author_email' => $comment->author_email,
        'author_name' => $comment->author_name,
        'id' => $comment->id
      );
      array_push($result, $comment_out);
    }
    return $result;
  }

  /**
   * URL for posting comments
   **/
  public function comment_action()
  {
    return URL::site('comment/create/'.$this->id);
  }
  /**
   * Generates ORM inputs for empty comment
   **/
  public function get_comment_inputs()
  {
    $comment = ORM::factory('Comment');
    $inputs = array();
    $inputs['author_email'] = Form::orm_textinput($comment, 'author_email');
    $inputs['author_name'] = Form::orm_textinput($comment, 'author_name');
    $inputs['content'] = Form::orm_textarea($comment, 'content');
    return $inputs;
  }
}
