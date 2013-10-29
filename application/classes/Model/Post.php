<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Post extends ORM {

  /**
   * @return array validation rules
   **/
  public function rules()
	{
		return array(
      'name' => array(
				array('not_empty'),
      ),
      'content' => array(
				array('not_empty'),
				array('min_length', array(':value', 4)),
      ),
      'draft' => array(
        array('numeric')
      ),
      'posted_at' => array(
        array('date')
      ),
		);
	}

  protected $_has_many = array(
    'comments' => array(
      'model' => 'Comment',
      'foreign_key' => 'post_id'
    ),
    'tags' => array(
      'model' => 'Tag',
      'through' => 'posts_tags'
    )
  );

  /**
   * Array of field labels.
   * Used in forms.
   **/
  protected $_labels = array(
    'name' => 'Заголовок',
    'content' => 'Текст записи',
    'is_draft' => 'Черновик',
    'posted_at' => 'Дата',
  );

  /**
   * Search term in all posts using Sphinx.
   * Note that Sphinx enforces hidden LIMIT 1,20
   **/
  public static function search($term)
  {
    $table = Kohana::$config->load('database')->get('sphinx')['connection']['database'];
    $db = Database::instance('sphinx');
    $result = $db->query(Database::SELECT, 'SELECT id FROM `'.$table.'` WHERE MATCH('.$db->quote($term).') LIMIT 100');
    return $result->as_array(NULL, 'id');
  }
}
