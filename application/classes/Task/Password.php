<?php defined('SYSPATH') OR die('No direct script access.');
 /**
  * This is a task to change admin's password.
  * It can accept -password option.
  * @category Helpers
  * @author Oreolek
  * @license AGPL
  **/
class Task_Password extends Minion_Task
{
  protected $_options = [
    'user' => 'admin',
    'password' => NULL,
  ];

  public function build_validation(Validation $validation)
  {
    return parent::build_validation($validation)
      ->rule('password', 'not_empty'); // Require this param
  }

  /**
   * This is an admin password task
   *
   * @return void
   */
  protected function _execute()
  {
    $params = $this->get_options();
    $writer = new Config_File_Writer;
    Kohana::$config->attach($writer);
    $config = Kohana::$config->load('auth');
    $hash = hash_hmac($config->get('hash_method'), $params['password'], $config->get('hash_key'));
    $users = $config->get('users');
    $users[$params['user']] = $hash;
    $config->set('users', $users);
    Kohana::$config->detach($writer);
    echo I18n::translate('The password was successfully changed.');
  }
}
