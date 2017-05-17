<?php defined('SYSPATH') OR die('No direct script access.');
/*
 *  Personal site oreolek.ru source code
 *  Copyright (C) 2014 Alexander Yakovlev
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Redefined HTML helper for HTML Tidy inclusion.
 **/
class HTML extends Kohana_HTML {
  /**
   * Tidies HTML using HTMLTidy. What's important that it closes all unclosed tags trimmed earlier.
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

  /**
   * Remove all <script> tags from HTML. Naive implementation, not for comments.
   **/
  public static function remove_scripts($html)
  {
    return preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
  }

  public static function style_tag($urls) {
    $out = '';
    foreach ($urls as $url) {
      $out .= '<link rel="preload" href="'.$url.'" as="style" onload="this.rel=\'stylesheet\'">';
      $out .= "\n";
    }
    $out .= '<noscript>';
    foreach ($urls as $url) {
      $out .= HTML::style($url);
      $out .= "\n";
    }
    $out .= '</noscript>';
  }
  public static function asyncscript($url) {
    return '<script async defer src="'.$url.'"></script>';
  }
}
