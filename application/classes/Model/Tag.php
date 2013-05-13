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

  protected $_belongs_to = array(
    'post' => array(
      'model' => 'Post',
      'through' => 'posts_tags'
    )
  );

}
