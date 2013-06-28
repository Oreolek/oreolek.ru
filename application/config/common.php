<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(
 'title' => 'Из Тишины',
 'author' => 'Александр Яковлев',
 'uploads_dir' => '/uploads',
 'thumbnail_width' => 200,
 'thumbnail_height' => 150,
 'comment_approval' => TRUE, // every comment must be approved
 'page_size' => 20, // pagination
 'comment_trust' => 5, // comment trust points - see Model_Comment::antispam_check
 'brief_limit' => 80, // word limit on brief descriptions
);
