function ajax_submit(mode_switch)
{
  post_date = moment(jQuery('input[name=posted_at]').val());
  current_date = moment();
  if (jQuery('input[name=is_draft]:checked').length > 0 && post_date <= current_date)
  {
    jQuery('input[name=posted_at]').val(moment(current_date).format());
  }
  jQuery.post(jQuery(".main_content form").attr("action"), {
      name: jQuery('input[name=name]').val(),
      content: jQuery('textarea[name=content]').val(),
      posted_at: jQuery('input[name=posted_at]').val(),
      mode: mode_switch
    },function(data) {
      data = jQuery.parseJSON(data)
      jQuery('#preview').html(data.preview);
      Hyphenator.run();
    }
  );
}

setInterval(ajax_submit(), 5*60*1000);
autosize(document.querySelector('textarea'));// somewhere need to have this
