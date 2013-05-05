<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_User extends Model_Auth_User {
  public function rules()
  {
    return array(
        'username' => array(
          array('not_empty'),
          array('min_length', 4),
          array('max_length', array(32)),
          array('username_available')
          ),
        'name' => array(
          array('not_empty'),
          array('max_length', array(255))
          ),
        'password' => array(
          array('not_empty'),
          array('min_length', array(5)),
          ),
        'email' => array(
          array('not_empty'),
          array('min_length', array(5)),
          array('max_length', array(127)),
          array('email'),
          array('email_available')
          )
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

