/**
 * Admin buttons to approve or reject comments
 * Dependency: jQuery
 **/
jQuery(document).ready(function(){
  $('input[type=checkbox]').click(function(){
    $.ajax({
      url: $(this).data('edit-url'),
      type: 'POST',
      data: { 'is_approved': $(this).prop("checked") }
    });
  });
});
