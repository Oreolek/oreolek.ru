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
    $temp .= $this->stat_scripts();
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
      'Дневник' => 'post/read',
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

  /**
   * Inline statistic scripts
   **/
  protected function stat_scripts()
  {
    $output = '';
    $yandex_metrika_id = Kohana::$config->load('stats.yandex_metrika_id');
    if (!empty($yandex_metrika_id))
    {
      $yandex_metrika = '<!-- Yandex.Metrika counter --><script type="text/javascript">(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter%s = new Ya.Metrika({id:%s, webvisor:true, clickmap:true, trackLinks:true, accurateTrackBounce:true}); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks");</script><noscript><div><img src="//mc.yandex.ru/watch/%s" style="position:absolute; left:-9999px;" alt="" /></div></noscript><!-- /Yandex.Metrika counter -->';
      $output .= sprintf($yandex_metrika, $yandex_metrika_id, $yandex_metrika_id, $yandex_metrika_id);
    }
    return $output;
  }

  public function search_url()
  {
    return Route::url('default', array('controller' => 'Post', 'action' => 'search'));
  }
}
