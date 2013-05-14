<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Tag extends ORM {
  /**
   * @return array validation rules
   **/
  public function rules()
	{
		return array(
      'name' => array(
				array('not_empty'),
      ),
		);
	}

  protected $_has_many = array(
    'posts' => array(
      'model' => 'Post',
      'through' => 'posts_tags'
    )
  );

  public $_labels = array(
    'name' => 'Название',
    'description' => 'Описание'
  );

}
