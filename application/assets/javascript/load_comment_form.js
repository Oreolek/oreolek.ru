jQuery(document).ready(function(){
  $('#new_comment').load($('#new_comment').data('url'), function(){
    $('#new_comment').html($('#new_comment').html().replace(/-ID-/g, $('#new_comment').data('id')));
    $('textarea').autosize();
  });
});
