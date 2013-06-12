<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Контроллер шаблона вёрстки
 **/
class View_Layout {
  public $_view = NULL;
  public $title = '';
  public $scripts = array();
  public $base_scripts = array(
    'hyphenator.min.js'
  );
  public $errors;
  
  public function has_errors()
  {
    return !empty($this->errors);
  }

  public function site_title()
  {
    if (Auth::instance()->logged_in())
    {
      return 'Добро пожаловать, '.Auth::instance()->get_user()->username;
    }
    else
    {
      return Kohana::$config->load('common.title');
    }
  }
  public function stylesheet()
  {
    return Less::compile(APPPATH.'assets/stylesheets/main');
  }

  public function get_content()
  {
    return Kostache::factory()->render($this->content);
  }

  public function scripts()
  {
    $scripts = array_merge ($this->scripts, $this->base_scripts);
    $temp = "";
    foreach($scripts as $script):
      if (strstr($script, '://') === FALSE) //no protocol given, script is local
      {
        $temp .= '<script type="text/javascript" charset="utf-8" src="'.URL::site('application/assets/javascript/'.$script).'"></script>'."\n";
      }
      else
      {
        $temp .= '<script type="text/javascript" charset="utf-8" src="'.$script.'"></script>'."\n";
      }
    endforeach;
    return $temp;
  }

  public function favicon()
  {
    return URL::site('favicon.ico');
  }

  public function navigation()
  {
    $result = array();
    $navigation = array(
      'Свежие записи дневника' => 'post/fresh',
      'Содержание дневника' => 'post/index',
      'Метки записей' => 'tag/index',
      'Список страниц' => 'page/index',
      'О сайте' => 'page/view/1',
    );
    if (!Auth::instance()->logged_in())
    {
      $navigation = array_merge($navigation, array('Вход' => 'user/signin'));
    }
    else
    {
      $navigation = array_merge($navigation, array(
        'Комментарии' => 'comment/index',
        'Черновики дневника' => 'post/drafts',
        'Черновики страниц' => 'page/drafts',
        'Заметки' => 'note/index',
      ));
    }

    foreach ($navigation as $key => $value)
    {
      array_push($result, array(
        'url' => URL::site('/'.$value),
        'title' => $key
      ));
    }
    return $result;
  }

  public function get_errors()
  {
    $result = array();
    foreach ($this->errors as $key => $string)
    {
      array_push($result, $string);
    }
    return $result;
  }
}
