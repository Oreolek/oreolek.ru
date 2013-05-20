<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Install extends Controller_Template {
  public $template = 'install/view';
  public function before()
  {
    parent::before();
    if ((Kohana::$environment == Kohana::PRODUCTION)) $this->request->redirect('');
  }
  /**
   * If database is not set, initialize and configure it; else nothing to do
   **/
  public function action_view()
  {
    $user = NULL;
    $username = Kohana::$config->load('database.default.connection.username');
    $password = Kohana::$config->load('database.default.connection.password');
    $host = Kohana::$config->load('database.default.connection.hostname');
    $database = Kohana::$config->load('database.default.connection.database');
    $db = Database::instance('default');

    try
    {
      $db->connect();
    }
    catch(Database_Exception $e)
    {
      DB::query(NULL,"CREATE DATABASE IF NOT EXISTS $database;")->execute();
    }

    try
    {
      $user = ORM::factory('User');
    }
    catch(Database_Exception $e)
    {
      // import MySQL dump; it's safer to use shell command than parse SQL by hand
      // if shell_exec is forbidden on your server, you have to import schema.sql manually
      shell_exec("mysql -u $username -p$password -h $host $database < ".Kohana::find_file('', 'schema', 'sql'));
      $db->connect();
    }

    $count_users = ORM::factory('User')->count_all();
    if ($count_users > 0) $this->redirect('install/finished');
    $this->template->errors = array();
    if (HTTP_Request::POST == $this->request->method())
    {
      $post = Arr::map('trim', $this->request->post());
      $user->values($post);
      try
      {
        $user->create();
        $login_role = new Model_Role(array('name' =>'login'));
        $admin_role = new Model_Role(array('name' =>'admin'));
        $user->add('roles',$login_role);
        $user->add('roles',$admin_role);
        Auth::instance()->login($user->username, $user->password);
        $this->redirect('install/finished');
      }
      catch (ORM_Validation_Exception $e)
      {
        $this->template->errors = $e->errors();
      }
    }
    $user->password = '';
    $this->template->user = $user;
    $this->template->header = Request::factory('header/standard')->post('title', 'Настройка административного аккаунта')->execute();
    $this->template->footer = Request::factory('footer/standard')->execute();
  }

  public function action_finished()
  {
    $this->template = new View('install/finished');
    $this->template->header = Request::factory('header/standard')->post('title', 'Установка закончена')->execute();
    $this->template->footer = Request::factory('footer/standard')->execute();
  }

  /**
   * Migrate from Wordpress
   * (maybe I'll add more options later)
   **/
  public function action_migrate_wp()
  {
    $this->template = new View('install/migrate_wp');
    $this->template->header = Request::factory('header/standard')->post('title', 'Миграция из Wordpress')->execute();
    $this->template->footer = Request::factory('footer/standard')->execute();
    $this->template->errors = array();
    $migrator = new Model_Migrator_Wordpress();
    if (HTTP_Request::POST == $this->request->method())
    {
      $migrator->set('hostname', $this->request->post('hostname'));
      $migrator->set('database', $this->request->post('database'));
      $migrator->set('username', $this->request->post('username'));
      $migrator->set('password', $this->request->post('password'));
      $migrator->set('prefix', $this->request->post('prefix'));
      if ($migrator->connect())
      {
        if ($migrator->migrate_pages() AND $migrator->migrate_posts() AND $migrator->migrate_comments())
        {
          $this->redirect('install/finished');
        }
      }
    }
    $this->template->model = $migrator;
  }
}
