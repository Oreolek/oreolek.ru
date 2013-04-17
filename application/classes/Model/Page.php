<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Page extends ORM 
{
  protected $_rules = array (
      'name' => array (
        'not_empty'  => true,
        ),
      'content' => array(
        'not_empty'  => true,
        'min_length' => array(4),
        ),
      );
}
