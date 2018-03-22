$('.date').each(function(){
  if ($(this).text() != '')
  {
    var day = moment($(this).text());
    $(this).text(day.fromNow()+", "+day.format('LL'));
  }
});
