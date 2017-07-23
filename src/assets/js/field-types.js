/**
 * @todo integrate this into admin.js to allow for file uploads,
 * color picker and other dynamic fields.
 */

/**
 *** callback_colorpicker ***********
 ************************************
 ************************************
 */
if (bbsa_object.callback_colorpicker) {
  $.each(bbsa_object.callback_colorpicker, function (index, value) {
    $this = value.section + '[' + value.id;
    $('input[name="' + $this + ']"]').wpColorPicker();

    if ($('select[name="' + $this + '_opacity]"]').hasClass('hidden')) {
      $('select[name="' + $this + '_opacity]"]').removeClass('hidden').chosen().addClass('hidden');
    } else {
      $('select[name="' + $this + '_opacity]"]').chosen();
    }
    $('select[name="' + $this + '_opacity]"]').trigger('chosen:updated');

    /**
     * .replace @ref  http://stackoverflow.com/a/3812077/558561
     */
    var str = $this + '_opacity';
    if (!$('input[name="' + $this + '_checkbox]"]').is(':checked')) {
      $('#' + str.replace(/[\[\]]/g, '_') + '__chosen').hide();
    }

    $('input[name="' + $this + '_checkbox]"]').on('change', function () {
      $('#' + str.replace(/[\[\]]/g, '_') + '__chosen').toggle();
    });
  });
}

/**
 *** callback_file ******************
 ************************************
 ************************************
 */
if (bbsa_object.callback_file) {
  $.each(bbsa_object.callback_file, function (index, value) {
    var file_frame,
      set_to_post_id = 0;

    window.formfield = '';

    $(document.body).on('click', 'input[type="button"].button.' + value.id + '-browse', function (e) {
      e.preventDefault();

      $this = $(this);
      window.formfield = $this.closest('td');

      // If the media frame already exists, reopen it.
      if (file_frame) {
        file_frame.uploader.uploader.param('post_id', set_to_post_id);
        file_frame.open();
        return;
      } else {
        // Set the wp.media post id so the uploader grabs the ID we want when initialised
        wp.media.model.settings.post.id = set_to_post_id;
      }

      // Create the media frame.
      file_frame = wp.media.frames.file_frame = wp.media({
        frame: 'post',
        state: 'insert',
        title: $this.data('uploader_title'),
        button: {
          text: $this.data('uploader_button_text')
        },
        library: {
          type: 'image'
        },
        multiple: false  // Set to true to allow multiple files to be selected
      });

      file_frame.on('menu:render:default', function (view) {
        // Store our views in an object.
        var views = {};

        // Unset default menu items
        view.unset('library-separator');
        view.unset('gallery');
        view.unset('featured-image');
        view.unset('embed');

        // Initialize the views in our view object.
        view.set(views);
      });

      // When an image is selected, run a callback.
      file_frame.on('insert', function () {
        var attachment = file_frame.state().get('selection').first().toJSON();

        //	console.log(attachment);
        //	console.log(window.formfield.find('input[type="text"]').attr('id'));

        window.formfield.find('input[type="text"]').val(attachment.url);
        window.formfield.find('#' + value.id + '_preview').html('<div class="img-wrapper" style="width:250px"><img src="' + attachment.url + '" alt="" ><a href="#" class="remove_file_button" rel="' + value.id + '">Remove Image</a></div>');

      });

      // Finally, open the modal
      file_frame.open();
    });

    $('input[type="button"].button.' + value.id + '-clear').on('click', function (e) {
      e.preventDefault();
      $(this).closest('td').find('input[type="text"]').val('');
      $(this).closest('td').find('#' + $(this).prop('id').replace('_clear', '_preview') + ' div.image').remove();
    });
    $('a.remove_file_button').on('click', function (e) {
      e.preventDefault();
      $(this).closest('td').find('input[type="text"]').val('');
      $(this).parent().slideUp().remove();
    });
  });
}