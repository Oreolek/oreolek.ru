setInterval(function() {
  jQuery.post(window.location.pathname, {
    name: jQuery('input[name=name]').val(),
    content: jQuery('textarea[name=content]').val()
  }, function(data) {
    data = jQuery.parseJSON(data)
    jQuery('#preview').html(data.preview);
    if (data.date) {
      jQuery('input[name=posted_at]').val(data['date'])
    }
  });
}, 5*60*1000);
