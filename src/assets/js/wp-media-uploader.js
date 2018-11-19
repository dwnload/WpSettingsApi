/* global jQuery */
/**
 * wpMediaUploader
 * Based on v1.0 2016-11-05 by Smartcat
 * @link https://github.com/smartcatdev/WP-Media-Uploader
 */
(function ($) {
  $.wpMediaUploader = function (options) {

    let settings = $.extend({
      target: '.FieldType_file', // The class wrapping the textbox
      uploaderTitle: 'Select or upload image', // The title of the media upload popup
      uploaderButton: 'Set image', // the text of the button in the media upload popup
      multiple: false, // Allow the user to select multiple images
      modal: false, // is the upload button within a bootstrap modal ?
    }, options);

    $('button.wpMediaUploader').each(function (index, value) {
      $(value).on('click', function (e) {
        e.preventDefault();
        let selector = $(this).closest('td'), custom_uploader;

        custom_uploader = wp.media({
          title: settings.uploaderTitle,
          button: {
            text: settings.uploaderButton
          },
          multiple: settings.multiple
        }).on('select', function () {
          let attachment = custom_uploader.state().get('selection').first().toJSON();
          selector.find('img').fadeOut('fast');
          selector.find('input').val(attachment.url);
          if (settings.modal) {
            $('.modal').css('overflowY', 'auto');
          }
        }).open();
      });
    });
  }
})(jQuery);
