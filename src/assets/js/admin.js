/** global jQuery, localStorage **/
(function ($) {
  'use strict'

  const WpSettingsApi = {
    localStorageItemId: '_wpSettingApiActiveTab',
    objects: {
      container: $('.Dwnload_WP_Settings_Api__container'),
      header: $('.Dwnload_WP_Settings_Api__header'),
      sticky: $('.Dwnload_WP_Settings_Api__sticky'),
      group: $('.Dwnload_WP_Settings_Api__group'),
      menu: $('.Dwnload_WP_Settings_Api__menu')
    },

    /**
     * Initiation.
     */
    init: function () {
      this.initUIBindings()
    },

    /**
     * Bind UI events.
     */
    initUIBindings: function () {
      this.objects.group.hide()
      this.showActiveMenuItem(WpSettingsApi.getActiveTab())
      this.showActiveForm(WpSettingsApi.getActiveTab())
      this.menuItemListener()
      this.inputArrayListener()
      this.repeaterFieldsListener()
      $(function () {
        $('input.color-picker').wpColorPicker()
      })
    },

    /**
     * Show the active menu item or the first item if
     * no data is stored in localStorage.
     *
     * @param {string} activeTab
     */
    showActiveMenuItem: function (activeTab) {
      if (
        activeTab !== '' &&
        WpSettingsApi.objects.menu.find(
          '[data-tab-id="' + activeTab + '"]').length
      ) {
        WpSettingsApi.objects.menu.find('[data-tab-id="' + activeTab + '"]')
          .addClass('active')
      } else {
        WpSettingsApi.objects.menu.find('a').first().addClass('active')
      }
    },

    /**
     * Show the active form or the first form if
     * no data is stored in localStorage.
     *
     * @param {string} activeTab
     */
    showActiveForm: function (activeTab) {
      const activeTabFormObject = WpSettingsApi.getActiveFormObject(activeTab)

      if (activeTab !== '' && activeTabFormObject.length) {
        activeTabFormObject.fadeIn('fast')
      } else {
        WpSettingsApi.objects.group.first().fadeIn()
      }
    },

    /**
     * Listen for menu item clicks to trigger show/hide form(s) and active
     * localStorage events.
     */
    menuItemListener: function () {
      WpSettingsApi.objects.menu.find('a[data-tab-id]')
        .on('click', function (e) {
          const clickedGroup = $(this).data('tab-id')

          WpSettingsApi.objects.menu.find('a').removeClass('active')
          $(this).addClass('active').trigger('blur')

          WpSettingsApi.setActiveTab(clickedGroup)
          WpSettingsApi.objects.group.hide()
          WpSettingsApi.getActiveFormObject(clickedGroup).fadeIn('fast')
          e.preventDefault()
        })
    },

    inputArrayListener: function () {
      WpSettingsApi.objects.container.on('click', 'a[class^="button docopy-"]', function (e) {
        e.preventDefault()

        const element = $(this).prev().find('li:last')
        const newKey = parseInt(element.data('repeatable')) + 1

        const clone = element.clone()
        clone.attr('data-repeatable', newKey)
        clone.find('input').val('')
        clone.find('a.button').attr('data-key', newKey)
        clone.insertAfter(element)
        return true
      })

      WpSettingsApi.objects.container.on('click', 'a[class^="button dodelete-"]', function (e) {
        e.preventDefault()

        const $key = $(this).data('key')

        $('li[data-repeatable="' + $key + '"]').remove()
      })
    },

    /**
     * Listen repeater field clicks.
     */
    repeaterFieldsListener: function () {
      const $repeater = $('.FieldType_repeater')

      $repeater.on('click', '[data-add]', function (e) {
        e.preventDefault()

        const $group = $(this)
          .closest('.FieldType_repeater')
          .find('[data-repeatable]')
        const count = $group.length
        const $clone = $group.first().clone()

        $clone.find('[id]').each(function () {
          this.id = this.id + '_' + count
        })
        $clone.find('[name]').each(function () {
          this.name = this.name.replace(/\d/g, count)
        })
        $clone.find('[value]').each(function () {
          this.value = ''
        })

        $clone.insertBefore($(this))
      })

      $repeater.on('click', '[data-remove]', function (e) {
        e.preventDefault()

        const $group = $(this)
          .closest('.FieldType_repeater')
          .find('[data-repeatable]')

        if ($group.length > 1) {
          $(this).closest('[data-repeatable]').remove()
        }
      })
    },

    /**
     * Sets the localStorage item.
     *
     * @param {string} id
     */
    setActiveTab: function (id) {
      if (typeof (localStorage) !== 'undefined') {
        localStorage.setItem(WpSettingsApi.localStorageItemId, id)
      }
    },

    /**
     * Gets the localStorage item.
     *
     * @returns {string}
     */
    getActiveTab: function () {
      let activeTab

      if (typeof (localStorage) !== 'undefined') {
        activeTab = localStorage.getItem(WpSettingsApi.localStorageItemId)
      }

      return activeTab !== null ? activeTab : ''
    },

    /**
     * Get the form object by ID.
     *
     * @param {string} id
     * @returns {*|HTMLElement}
     */
    getActiveFormObject: function (id) {
      return $('#' + id)
    }
  }

  $(document).ready(() => WpSettingsApi.init())
}(jQuery))
