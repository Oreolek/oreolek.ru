<?php defined('SYSPATH') OR die('No direct script access.');

class ORM extends Kohana_ORM {
  /**
   * Gets label for field from _labels array
   * @retval string
   **/
  public function get_label($field)
  {
    return Arr::get($this->_labels, $field);
  }

  /**
   * Returns human-readable creation date
   **/
  public function creation_date()
  {
    return $this->posted_at;
  }

  /**
   * Create validation object from model rules.
   * @param array $_POST data
   * @return Validation
   **/
  public function validate_create($post_data) 
	{
		$validation = Validation::factory($post_data);
    $rules = $this->rules();
    foreach ($rules as $field => $rules)
    {
      $validation->rules($field, $rules);
    }
		return $validation;
	}

  /**
   * Loads model(s) from array of IDs or single ID.
   **/
  public function load_by_id($id)
  {
    if (empty($id))
      return FALSE;
    if (is_array($id))
      return $this->where('id', 'IN', $id)->find_all();
    return $this->where('id', '=', $id)->find();
  }
}
