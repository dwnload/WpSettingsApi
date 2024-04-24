/* global jQuery, wp */
/**
 * @copyright 2020
 * @link https://rudrastyh.com/wordpress/customizable-media-uploader.html
 */
(function ($) {
  const $body = $('body')
  $body.on('click', 'button.wpMediaUploader', function (e) {
    e.preventDefault()
    let attachment
    const button = $(this)
    const imageId = button.next().next().val()

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
      attachment = media.state().get('selection').first().toJSON()
      button.prev().val(attachment.url)
    })

    // already selected images
    media.on('open', function () {
      if (imageId) {
        const selection = media.state().get('selection')
        attachment = wp.media.attachment(imageId)
        attachment.fetch()
        selection.add(attachment ? [attachment] : [])
      }
    })

    media.open()
  })
})(jQuery)
