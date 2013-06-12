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
   * Checks comment content for spam.
   * every comment gets 5 trust points (configurable)
   * useragent empty = -2 points
   * comment not russian = -2 points
   * short = -1 point
   * etc.
   * @return boolean is the comment legit
   **/
  public function antispam_check($userbrowser = '')
  {
    $points = Kohana::$config->load('common.comment_trust');
    // check for suspicious/empty browser
    if (empty($userbrowser))
    {
      $points -= 2;
    }
    // check if comment contains HTML tags
    if (preg_match('<[\w!-]+\s.*>', $this->content) != FALSE)
    {
      $points -= 4;
    }
    // is it in russian?
    if (preg_match('[а-яА-Я]', $this->content) === FALSE)
    {
      $points -= 2;
    }
    // is it short?
    if (mb_strlen($this->content) < 25)
    {
      $points -= 1;
    }
    if ($points <= 0)
      return FALSE;
    return TRUE;
  }
}
