<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Signing in view controller
 **/
class View_User_Signin extends View_Edit {
  public function is_development()
  {
    return (Kohana::$environment == Kohana::DEVELOPMENT);
  }
  /**
   * Link for restoration
   **/
  public function password_link()
  {
    return Route::url('default', array('controller' => 'User', 'action' => 'password'));
  }
}
