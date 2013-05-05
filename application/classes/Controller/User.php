<?php defined('SYSPATH') or die('No direct script access.');

/**
 * User controller.
 * Only sign in and edit. The only administration user is created in the install process.
 **/
class Controller_User extends Controller_Layout {
  public function action_view(){$this->request->redirect('');}
  public function action_signin()
  {
  }
}
