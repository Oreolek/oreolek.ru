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
 * Comment view controller.
 * Displays all comments for a specific post.
 **/
class View_Comment_View extends View_Layout {
  public $comments;
  public function get_comments()
  {
    $result = array();
    foreach ($this->comments as $comment)
    {
      $comment_out = array(
        'content' => Markdown::instance()->transform($comment->content),
        'author_email' => $comment->author_email,
        'author_name' => $comment->author_name,
        'id' => $comment->id
      );
      if (empty($comment->author_name))
      {
        $comment_out['author_name'] = Kohana::$config->load('common')->get('anonymous_name');
      }
      array_push($result, $comment_out);
    }
    return $result;
  }
}
