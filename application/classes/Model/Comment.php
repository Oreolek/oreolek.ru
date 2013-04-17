<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Comment extends ORM 
{
  protected $_rules = array (
      'author_name' => array (
        ),
      'author_email' => array (
        'email' => true,
        ),
      'content' => array(
        'not_empty'  => true,
        'min_length' => array(4),
        'regex' => Markdown::$validate
        ),
      'post_id' => array (
        'numeric' => true,
        ),
      );
  protected $_belongs_to = array(
      'post' => array(
        'model' => 'Post',
        'foreign_key' => 'post_id'
        )
      );
}
