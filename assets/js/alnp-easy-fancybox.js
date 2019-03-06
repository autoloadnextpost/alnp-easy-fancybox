$(function() {
  $('body').on( 'alnp-post-loaded', function( e, params ) {
    $(document.body).trigger('post-load');
  });
})(jQuery);