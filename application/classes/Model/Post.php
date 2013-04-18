<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Post extends ORM {
  protected $_rules = array(
    'name' => array(
      'not_empty'  => true,
    ),
    'content' => array(
      'not_empty'  => true,
      'min_length' => array(4),
    ),
  );

  protected $_has_many = array(
    'comments' => array(
      'model' => 'Comment',
      'foreign_key' => 'post_id'
    )
  );

  /**
   * Array of field labels.
   * Used in forms.
   **/
  protected $_labels = array(
    'name' => 'Заголовок',
    'content' => 'Текст записи',
  );
}
