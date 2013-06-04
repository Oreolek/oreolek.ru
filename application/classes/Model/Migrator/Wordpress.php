<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Migrator from Wordpress to this engine.
 * No taxonomy (tags & categories) at the moment.
 **/
class Model_Migrator_Wordpress extends Model_Migrator {
  protected $posts_done;
  public function __construct()
  {
    parent::__construct();
    $this->posts_done = false;
  }
  /**
   * Migrate posts and pages. Wordpress keeps them all in one table.
   **/
  public function migrate_posts() {
		$sql = sprintf('SELECT `ID`, `post_date`, `post_content`, `post_title`, `post_status`, `post_type`
      FROM %sposts AS wp_posts
			WHERE post_type IN ("page", "post")', $this->get('prefix'));
		$wp_posts = $this->source_database->query(Database::SELECT,$sql)->as_array();

    $post_instance = new Model_Post;
    $table_posts = $post_instance->table_name();
    unset($post_instance);
    DB::delete($table_posts)->execute();

    $page_instance = new Model_Page;
    $table_pages = $page_instance->table_name();
    unset($page_instance);
    DB::delete($table_pages)->execute();
    
    foreach($wp_posts as $post) {
      $is_draft = (int) ($post['post_status'] === 'draft');
      $table = $table_posts;
      if ($post['post_type'] === 'page')
      {
        $table = $table_pages;
      }
      DB::insert($table, array('id', 'name', 'content', 'is_draft', 'posted_at'))
        ->values(array($post['ID'], $post['post_title'], $post['post_content'], $is_draft, $post['post_date']))
        ->execute();
	  }
    $this->posts_done = true;
    return true;
  }
  public function migrate_pages() {
    if ($this->posts_done) return true;
    return $this->migrate_posts();
  }
  public function migrate_comments() {
    $sql = sprintf('SELECT `comment_post_ID`, `comment_author`, `comment_author_email`, `comment_content`, `comment_date`
      FROM %scomments AS wp_comments
			WHERE comment_type != "trackback" AND comment_approved = "1"', $this->get('prefix'));
    
    $wp_comments = $this->source_database->query(Database::SELECT,$sql)->as_array();
    $comment_instance = new Model_Comment;
    $table = $comment_instance->table_name();
    unset($comment_instance);
    DB::delete($table)->execute();
      
    foreach ($wp_comments as $comment)
    {
      DB::insert($table, array('post_id', 'author_name', 'author_email', 'content', 'posted_at', 'is_approved'))
        ->values(array(
          $comment['comment_post_ID'],
          $comment['comment_author'],
          $comment['comment_author_email'],
          $comment['comment_content'],
          $comment['comment_date'],
          Model_Comment::STATUS_APPROVED
        ))
        ->execute();
    }
    return true;
  }
}
