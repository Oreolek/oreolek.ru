<?php defined('SYSPATH') OR die('No direct access allowed.');
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
 * Photo model. Image thumbnailing and (hopefully) file uploads.
 **/
class Model_Photo extends ORM {

  /**
   * @return array validation rules
   **/
  public function rules()
	{
		return array(
      'name' => array(
				array('not_empty'),
      ),
      'filename' => array(
        array('not_empty')
      )
    );
  }

  public $_labels = array(
    'name' => 'Название',
  );

  protected function get_image_dir_path()
  {
    return Kohana::$config->load('common.uploads_dir').'/photos';
  }
  public function get_image_path()
  {
    if ($this->filename) return $this->get_image_dir_path().'/'.$this->filename;
  }

  /**
	 * Function that adds suffix _thumb to file name: /home/dhawu.jpeg -> /home/dhawu_thumb.jpeg
	 * @param integer $width
	 * 	thumbnail width (manages the suffix)
	 * @param integer $height
	 *	thumbnail height (manages the suffix)
	 * @retval string
	 */
  public function get_thumbnail_path($width = NULL, $height = NULL)
  {
		$image_path = $this->get_image_path();
    return $this->generate_thumbnail($image_path, $width, $height);
  }

  public static function generate_thumbnail($image_path, $width = 0, $height = 0)
  {
    if ($width == 0) $width = Kohana::$config->load('common.thumbnail_width');
		if ($height == 0) $height = Kohana::$config->load('common.thumbnail_height');
    if (!is_file(DOCROOT.$image_path))
    {
      throw new HTTP_Exception_404('File not found');
      return $image_path;
    }

    $thumbnail_path = self::generate_thumbnail_path($image_path, $width, $height);

		if (!is_file(DOCROOT.$thumbnail_path)) {
			$image = Image::factory(DOCROOT.$image_path);
      if ($image->height < $height)
      {
        $height = $image->height;
      }
      if ($image->width < $width)
      {
        $width = $image->width;
      }
			$image->resize($width, $height, Image::WIDTH); 
			$image->crop($width, $height);
			$image->save(DOCROOT.$thumbnail_path, 80); //save thumbnail with quality of 80
		}
		return $thumbnail_path;
  }

  public static function generate_thumbnail_path($image_path, $width = 0, $height = 0)
  {
    if ($width == 0) $width = Kohana::$config->load('common.thumbnail_width');
		if ($height == 0) $height = Kohana::$config->load('common.thumbnail_height');
    $parts = explode('.', $image_path);
		$count = count($parts) - 2;
		if ($count < 0) $count = 0;
		$suffix = 'thumb';
		if ($width) $suffix .= $width;
		if ($height) $suffix .= '_' . $height;
		$parts[$count] .= '_' . $suffix;
    return implode('.', $parts);
  }

  protected function get_thumbnail_file_path($width = NULL, $height = NULL)
  {
    return DOCROOT.$this->get_thumbnail_path($width, $height);
  }

  public function file_save($file)
  {
    return Upload::save($file, $this->filename, DOCROOT.$this->get_image_dir_path());
  }
}
