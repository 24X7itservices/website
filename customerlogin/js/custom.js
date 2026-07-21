$(document).on('click', '.sidebar-toggle', function (e) {
  e.preventDefault();
  $('html').toggleClass('sidebar-left-collapsed');
  $(document).trigger('sidebar-left-toggle');
});