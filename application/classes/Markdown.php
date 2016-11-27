<?php defined('SYSPATH') or die('No direct script access.');
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
 * Markdown class with custom tags.
 **/
class Markdown extends \cebe\markdown\MarkdownExtra {
  public $html5 = TRUE;
  protected function image_regex()
  {
    return '{
      ( # wrap whole match in $1
      !thumb\[
        ('.$this->nested_brackets_re.')    # alt text = $2
        \]
        \s?      # One optional whitespace character
        \(      # literal paren
          [ \n]*
          (?:
          <(\S*)>  # src url = $3
          |
          ('.$this->nested_url_parenthesis_re.')  # src url = $4
        )
        [ \n]*
        (      # $5
          ([\'"])  # quote char = $6
          (.*?)    # title = $7
          \6    # matching quote
          [ \n]*
        )?      # class is optional
        \)
      )
    }xs';
  }

  protected function identifyImage($line, $lines, $current)
  {
    // if a line starts with at least 3 backticks it is identified as a fenced code block
    if (preg_match($this->image_regex())
    {
      return 'localImages';
    }
  }

  /**
   * Turn custom image shortcuts into <img> tags with preview.
   * Shortcut is: !thumb[alt text](url "optional title")
   * @see do_images
   *
   * @param   string   The markdown getting transformed.
   * @return  string   String that will replace the tag.
   */
  protected function consumeLocalImages($text)
  {
    $whole_match  = $matches[1];
    $alt_text    = $matches[2];
    $url      = $matches[3] == '' ? $matches[4] : $matches[3];
    $class      =& $matches[7];

    $alt_text = $this->encode_attribute($alt_text);
    $url = $this->encode_attribute($url);
    $src = NULL;
    $uid = uniqid();
    $result = '<a href="'.$url.'" data-lightbox="'.$uid.'" title="'.$alt_text.'"><img alt="'.$alt_text.'" title="'.$alt_text.'"';
    try {
      $src = Model_Photo::generate_thumbnail($url);
    }
    catch(HTTP_Exception_404 $e)
    {
      // file not found, but Markdown shouldn't bother about that
      $src = NULL;
    }
    if (isset($src))
    {
      $result .= ' src="'.$src.'"';
    }
    if (isset($class))
    {
      $result .= ' class="'.$class.'"';
    }

    $result .= $this->suffix.'</a>';

    return $this->hash_part($result);
  }

  protected function renderLocalImages($block)
  {
  }

}
