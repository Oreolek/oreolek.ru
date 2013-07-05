<?php defined('SYSPATH') or die('No direct script access.');
require_once(APPPATH.'vendors/smartypants/smartypants.php');

class Markdown extends Kohana_Markdown {
  /* Transformations that occur *within* block-level tags */
	protected $span_gamut = array(
		"parseSpan"				=> -30,
    "do_local_images" => 5,
		"do_images"				=> 10,
		"do_anchors"			=> 20,

		"doAutoLinks"			=> 30,
    "do_smartypants"    => 35,
		"encode_amps_and_angles"	=> 40,

		"do_italics_and_bold"	=> 50,
		"do_hard_breaks"		=> 60,
  );

  /**
	 * Turn custom image shortcuts into <img> tags with preview.
   * Shortcut is: !thumb[alt text](url "optional title")
   * @see do_images
	 *
	 * @param   string   The markdown getting transformed.
	 * @return  string   String that will replace the tag.
	 */
	protected function do_local_images($text)
	{
		$text = preg_replace_callback('{
			(				# wrap whole match in $1
			  !thumb\[
				('.$this->nested_brackets_re.')		# alt text = $2
			  \]
			  \s?			# One optional whitespace character
			  \(			# literal paren
				[ \n]*
				(?:
					<(\S*)>	# src url = $3
				|
					('.$this->nested_url_parenthesis_re.')	# src url = $4
				)
				[ \n]*
				(			# $5
				  ([\'"])	# quote char = $6
				  (.*?)		# title = $7
				  \6		# matching quote
				  [ \n]*
				)?			# class is optional
			  \)
			)
			}xs',
			array(&$this, '_do_local_images_callback'), $text);

		return $text;
	}

  protected function _do_local_images_callback($matches)
  {
    $whole_match	= $matches[1];
		$alt_text		= $matches[2];
		$url			= $matches[3] == '' ? $matches[4] : $matches[3];
		$class			=& $matches[7];

		$alt_text = $this->encode_attribute($alt_text);
		$url = $this->encode_attribute($url);
    $src = NULL;
    $result = "<a href=\"$url\"><img alt=\"$alt_text\"";
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

  /**
   * Smartypants transformation
   * @see APPPATH/vendors/smartypants/smartypants.php
   **/
  protected function do_smartypants($text)
  {
    return Smartypants($text);
  }

  protected function run_span_gamut($text)
	{
		foreach ($this->span_gamut as $method => $priority) {
			$text = $this->$method($text);
		}

		return $text;
	}

}
