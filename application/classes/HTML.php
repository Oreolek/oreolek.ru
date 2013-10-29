<?php defined('SYSPATH') OR die('No direct script access.');

class HTML extends Kohana_HTML {
  /**
   * Tidies HTML using HTMLTidy.
   * @param string $html
   * @return string
   **/
  public static function tidy($html)
  {
    $tidy_config = array(
      'clean' => TRUE,
      'output-html' => TRUE,
      'show-body-only' => TRUE,
      'wrap' => 0,
    ); 
    $tidy = new tidy();
    $tidy->ParseString($html, $tidy_config, 'utf8');
    $tidy->cleanRepair();
    return $tidy->body(); 
  }
}
