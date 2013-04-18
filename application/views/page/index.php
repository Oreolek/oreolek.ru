<?php echo Request::factory('header/standard')->post('title',$title)->execute() ?>

<h2><?php echo $title ?></h2>
<div class="table_index">
<?php
foreach ($pages as $page)
{
  echo '<div class="date">'. $page->posted_at .'</div>';
  echo '<div class="link"><a href = "'. URL::site('page/view/' . $page->id). '">' . $page->name . '</a></div>';
}
?>
</div>

<?php echo Request::factory('footer/standard')->execute() ?>
