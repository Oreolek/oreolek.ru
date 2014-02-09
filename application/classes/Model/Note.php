<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Note extends ORM {

  /**
   * @return array validation rules
   **/
  public function rules()
	{
		return array(
      'name' => array(
				array('not_empty'),
      ),
      'content' => array(
				array('not_empty'),
				array('min_length', array(':value', 4)),
      )
		);
	}

  /**
   * Array of field labels.
   * Used in forms.
   **/
  protected $_labels = array(
    'name' => 'Заголовок',
    'password' => 'Пароль',
    'content' => 'Текст заметки',
  );
}
