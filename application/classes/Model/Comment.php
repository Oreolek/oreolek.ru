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
        ),
      'post_id' => array (
        'numeric' => true,
        ),
      );
}
