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
	 * Binds another one-to-one object to this model.  One-to-one objects
	 * can be nested using 'object1:object2' syntax
	 *
	 * @param  string $target_path Target model to bind to
	 * @return void
   * @author Sverri [http://forum.kohanaframework.org/profile/6363/Sverri]
	 */
	public function with_count($target_path, $_label)
	{
		if (isset($this->_with_applied[$target_path]))
		{
			// Don't join anything already joined
			return $this;
		}
		// Split object parts
		$aliases = explode(':', $target_path);
		$target = $this;
		foreach ($aliases as $alias)
		{
			// Go down the line of objects to find the given target
			$parent = $target;
			$target = $parent->_related_many($alias);

			if ( ! $target)
			{
				// Can't find related object
				return $this;
			}
		}
		// Target alias is at the end
		$target_alias = $alias;

		// Pop-off top alias to get the parent path (user:photo:tag becomes user:photo - the parent table prefix)
		array_pop($aliases);
		$parent_path = implode(':', $aliases);

		if (empty($parent_path))
		{
			// Use this table name itself for the parent path
			$parent_path = $this->_object_name;
		}
		else
		{
			if ( ! isset($this->_with_applied[$parent_path]))
			{
				// If the parent path hasn't been joined yet, do it first (otherwise LEFT JOINs fail)
				$this->with_count($parent_path);
			}
		}
		// Add to with_applied to prevent duplicate joins
		$this->_with_applied[$target_path] = TRUE;

		// Create a count expression
		$name = DB::expr('COUNT(`'.$target_alias.'`.`id`)');

		// Use the provided label as the alias
		$alias = $_label;
		$this->select(array($name, $alias));

		// Parent has_one target, use parent's primary key as target's foreign key
		$join_col1 = $parent_path.'.'.$parent->_primary_key;
		$join_col2 = $target_path.'.'.$parent->_has_many[$target_alias]['foreign_key'];

		// Join the related object into the result
		$this->join(array($target->_table_name, $target_path), 'LEFT')->on($join_col1, '=', $join_col2);

		// Group by the parent (will not work otherwise)
		$this->group_by(array($parent_path, $parent->_primary_key));

		return $this;
	}

	protected function _related_many($alias)
	{
		if (isset($this->_related[$alias]))
		{
			return $this->_related[$alias];
		}
		elseif (isset($this->_has_many[$alias]))
		{
			return $this->_related[$alias] = ORM::factory($this->_has_many[$alias]['model']);
		}
		else
		{
			return FALSE;
		}
	}
  
  /**
   * Loads model(s) from array of IDs or single ID.
   **/
  public function load_by_id($id)
  {
    if (empty($id))
      return FALSE;
    if (is_array($id))
      return $this->where($this->_object_name .'.'. $this->_primary_key, 'IN', $id)->find_all();
    return $this->where('id', '=', $id)->find();
  }
}
