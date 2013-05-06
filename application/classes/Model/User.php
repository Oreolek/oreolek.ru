<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_User extends Model_Auth_User {
  public function rules()
  {
    return array_merge(parent::rules(), array(
        'name' => array(
          array('not_empty'),
          array('max_length', array(':value', 255)),
          ))
        );
  }

  /**
   * Array of field labels.
   * Used in forms.
   **/
  protected $_labels = array(
      'username' => 'Имя пользователя (логин)',
      'name' => 'Отображаемое имя',
      'password' => 'Пароль пользователя',
      'email' => 'Email',
      );
}

