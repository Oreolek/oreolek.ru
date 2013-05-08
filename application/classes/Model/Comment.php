<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Comment extends ORM 
{
  protected $_rules = array (
      'author_name' => array (
        'not_empty'  => true,
        'max_length' => array(32),
        ),
      'author_email' => array (
        'email' => true,
        'max_length' => array(127),
        ),
      'content' => array(
        'not_empty'  => true,
        'min_length' => array(4),
        'regex' => '/[[:punct:][:space:]]{0,}/'
        ),
      'post_id' => array (
        'numeric' => true,
        'not_empty'  => true,
        ),
      );
  protected $_belongs_to = array(
      'post' => array(
        'model' => 'Post',
        'foreign_key' => 'post_id'
        )
      );
  /**
   * Array of field labels.
   * Used in forms.
   **/
  protected $_labels = array(
    'author_name' => 'Имя комментатора',
    'author_email' => 'Ваш e-mail',
    'content' => 'Комментарий'
  );
}
