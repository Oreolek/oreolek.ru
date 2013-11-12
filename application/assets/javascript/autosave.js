function ajax_submit(mode_switch)
{
  jQuery.post(jQuery(".main_content form").attr("action"), {
      name: jQuery('input[name=name]').val(),
      content: jQuery('textarea[name=content]').val(),
      mode: mode_switch
    },function(data) {
      data = jQuery.parseJSON(data)
      jQuery('#preview').html(data.preview);
      if (data.date) {
        jQuery('input[name=posted_at]').val(data['date'])
      }
    }
  );
}

setInterval(ajax_submit(), 5*60*1000);
