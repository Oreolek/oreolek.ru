<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Comment extends ORM 
{
  const STATUS_PENDING = 0;
  const STATUS_APPROVED = 1;
  const STATUS_SUSPICIOUS = 2;
  const STATUS_SPAM = 3;
  /**
   * @return array validation rules
   **/
  public function rules()
	{
		return array(
      'author_name' => array(
				array('not_empty'),
				array('max_length', array(':value', 32)),
      ),
      'author_email' => array(
				array('not_empty'),
        array('email'),
				array('max_length', array(':value', 127)),
      ),
      'content' => array(
				array('not_empty'),
				array('min_length', array(':value', 4)),
      ),
      'post_id' => array(
        array('not_empty'),
        array('numeric')
      )
		);
	}

  protected $_belongs_to = array(
      'post' => array(
        'model' => 'Post',
        'foreign_key' => 'post_id'
        )
      );
  /**
   * Array of field labels.
   * Used in forms.
   **/
  protected $_labels = array(
    'author_name' => 'Имя комментатора',
    'author_email' => 'Ваш e-mail',
    'content' => 'Комментарий',
    'is_approved' => 'Проверено'
  );

  /**
   * Antispam check for useragent string.
   * Suspects user if useragent is empty.
  **/
  public static function useragent_check($useragent = '')
  {
    if (empty($userbrowser))
      return FALSE;
    return TRUE;
  }

  /**
   * Checks comment content for spam.
   * every comment gets 5 trust points (configurable)
   * useragent empty = -2 points
   * comment not russian = -2 points
   * short = -1 point
   * etc.
   * @return boolean is the comment legit
   **/
  public static function antispam_check($content = '')
  {
    $points = Kohana::$config->load('common.comment_trust');
    // check if comment contains HTML tags
    if (preg_match('/<[\w!-]+\s.*>/', $content) != FALSE)
    {
      $points -= 4;
    }
    // check if it contains URLs
    $urls = preg_match_all('/(([A-Za-z]{3,9}:(?:\/\/)?)|(?:www\.|[\-;:&=\+\$,\w]+@))[A-Za-z0-9а-яА-Я\.\-]+/', $content);
    $points -= 3 * $urls;
    // is it in russian?
    if (preg_match('/[а-яА-Я]/', $content) === 0)
    {
      $points -= 3;
    }
    // is it short? (less than five words)
    if (substr_count($content, ' ') < 5)
    {
      $points -= 2;
    }
    if ($points <= 0)
      return FALSE;
    return TRUE;
  }

  /**
   * Get date of latest approved comment by post ID
   **/
  public static function get_latest_date($id)
  {
    $query = DB::select(array(DB::expr('MAX(`posted_at`)'), 'max_date'))->from('comments')->where('post_id', '=', $id)->and_where('is_approved', '=', self::STATUS_APPROVED);
    return $query->execute()->get('max_date');
  }
}
