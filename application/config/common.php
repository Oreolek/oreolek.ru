<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(
 'title' => 'Из Тишины',
 'author' => 'Александр Яковлев',
 'author_about' => 'Чаеголик, сайтобурист, картофан.',
 'author_img' => '/application/assets/images/author_small.png',
 'patreon_img' => '/application/assets/images/patreon.png',
 'author_img_alt' => 'Фотограф Евгения Цвеклинская, 2014',
 'author_email' => 'keloero@oreolek.ru',
 'uploads_dir' => '/uploads',
 'thumbnail_width' => 500,
 'thumbnail_height' => 300,
 'comment_approval' => TRUE, // every comment must be approved
 'page_size' => 7, // pagination: items on page. Small numbers are good on mobiles.
 'page_display' => 17, // pagination: pages to display
 'comment_trust' => 5, // comment trust points - see Model_Comment::antispam_check
 'brief_limit' => 80, // word limit on brief descriptions
 'anonymous_name' => 'Серый голос'
);
