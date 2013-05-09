<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_User extends Model_Auth_User {
  /**
   * Array of field labels.
   * Used in forms.
   **/
  protected $_labels = array(
      'username' => 'Имя пользователя',
      'password' => 'Пароль пользователя',
      'email' => 'Email',
      );
}
