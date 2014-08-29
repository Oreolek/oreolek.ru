WebFontConfig = {
  google: { families: [ 'PT+Sans:400,400italic,700:latin,cyrillic' ] }
};
(function() {
  var wf = document.createElement('script');
  wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
  '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
  wf.type = 'text/javascript';
  wf.async = 'true';
  var s = document.getElementsByTagName('script')[0];
  s.parentNode.insertBefore(wf, s);
})();

$('.date').each(function(){
  if ($(this).text() != '')
  {
    day = moment($(this).text());
    $(this).text(day.fromNow()+", "+day.format('LL'));
  }
});
