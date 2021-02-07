/* global jQuery, wp */
/**
 * @copyright 2020
 * @link https://rudrastyh.com/wordpress/customizable-media-uploader.html
 */
(function ($) {
  const $body = $('body')
  $body.on('click', 'button.wpMediaUploader', function (e) {
    e.preventDefault()
    const button = $(this)
    const media = wp.media({
      title: 'Insert image',
      library: {
        type: 'image'
      },
      button: {
        text: 'Use this image' // button label text
      },
      multiple: false
    }).on('select', function () { // it also has "open" and "close" events
      const attachment = media.state().get('selection').first().toJSON()
      console.log(attachment)
      // button.html('<img src="' + attachment.url + '">')
      button.prev().val(attachment.url)
    }).open()
  })
})(jQuery)
