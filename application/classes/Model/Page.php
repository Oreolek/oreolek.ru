<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Page extends ORM {
  /**
   * Validation rules array
   **/
  protected $_rules = array (
      'name' => array (
        'not_empty'  => true,
        ),
      'content' => array(
        'not_empty'  => true,
        'min_length' => array(4),
        ),
      );

  /**
   * Array of field labels.
   * Used in forms.
   **/
  protected $_labels = array(
    'name' => 'Заголовок',
    'content' => 'Текст страницы'
  );
}
