$('.date').each(function(){
  if ($(this).text() != '')
  {
    day = moment($(this).text());
    $(this).text(day.fromNow()+", "+day.format('LL'));
  }
});
