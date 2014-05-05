<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Layout extends Controller {
  protected $secure_actions = FALSE;
  protected $is_private = FALSE;
  public $auto_render = TRUE;
  public $template = '';

  public function before()
  {
    parent::before();
    $action_name = $this->request->action();
    // clear cache in dev mode
    if (Kohana::$environment == Kohana::DEVELOPMENT)
    {
      Cache::instance('apcu')->delete_all();
    }
    if (
      is_array($this->secure_actions) &&
      array_key_exists($action_name, $this->secure_actions) &&
      Kohana::$environment != Kohana::DEVELOPMENT
    )
    {
      if ( Auth::instance()->logged_in($this->secure_actions[$action_name]) === FALSE)
      {
        if (Auth::instance()->logged_in())
        {
          $this->redirect('error/403');
        }
        else
        {
          $this->redirect('user/signin');
        }
      }
      else
      {
        //user is clear to go but his pages are cache-sensitive
        $this->is_private = TRUE;
      }
    }
    // force https everywhere
    if (Kohana::$config->load('common')->get('force_https'))
    {          
      if (!$this->request->secure())
      {
        $this->request->secure(TRUE);
      } 
    }
  }
  public function after()
  {
    if ($this->auto_render)
    {
      $renderer = Kostache_Layout::factory('layout');
      $this->response->body($renderer->render($this->template, $this->template->_view));
    }
    if ($this->is_private)
    {
      $this->response->headers( 'cache-control', 'private' );
      $this->check_cache();
    }
  }
}
