/* global jQuery **/
(function ($) {
  'use strict'

  let Dwnload_WP_Settings = {
    localStorageItemId: 'activeTab',
    objects: {
      container: $('.Dwnload_WP_Settings_Api__container'),
      header: $('.Dwnload_WP_Settings_Api__header'),
      sticky: $('.Dwnload_WP_Settings_Api__sticky'),
      group: $('.Dwnload_WP_Settings_Api__group'),
      menu: $('.Dwnload_WP_Settings_Api__menu')
    },

    /**
     * Initiate the JS
     */
    init: function () {
      this.initUIBindings()
      this.submitListener()
    },

    initUIBindings: function () {
      this.objects.group.hide()
      this.showActiveMenuItem(Dwnload_WP_Settings.getActiveTab())
      this.showActiveForm(Dwnload_WP_Settings.getActiveTab())
      this.menuItemListener()
    },

    /**
     * Show the active menu item or the first item if
     * no data is stored in localStorage.
     *
     * @param {string} activeTab
     */
    showActiveMenuItem: function (activeTab) {
      if (activeTab !== '' && Dwnload_WP_Settings.objects.menu.find('[data-tab-id="' + activeTab + '"]').length) {
        Dwnload_WP_Settings.objects.menu.find('[data-tab-id="' + activeTab + '"]')
          .addClass('active')
      } else {
        Dwnload_WP_Settings.objects.menu.find('a').first()
          .addClass('active')
      }
    },

    /**
     * Show the active form or the first form if
     * no data is stored in localStorage.
     *
     * @param {string} activeTab
     */
    showActiveForm: function (activeTab) {
      let activeTabFormObject = Dwnload_WP_Settings.getActiveFormObject(activeTab)

      if (activeTab !== '' && activeTabFormObject.length) {
        activeTabFormObject.fadeIn('fast')
      } else {
        Dwnload_WP_Settings.objects.group.first().fadeIn()
      }
    },

    /**
     * Listen for menu item clicks to trigger show/hide form(s) and active
     * localStorage events.
     */
    menuItemListener: function () {
      Dwnload_WP_Settings.objects.menu.find('a[data-tab-id]').on('click', function (e) {
        let clickedGroup = $(this).data('tab-id')

        Dwnload_WP_Settings.objects.menu.find('a').removeClass('active')
        $(this).addClass('active').blur()

        Dwnload_WP_Settings.setActiveTab(clickedGroup)
        Dwnload_WP_Settings.objects.group.hide()
        Dwnload_WP_Settings.getActiveFormObject(clickedGroup).fadeIn('fast')
        e.preventDefault()
      })
    },

    /**
     * Sets the localStorage item.
     *
     * @param {string} id
     */
    setActiveTab: function (id) {
      if (typeof(localStorage) !== 'undefined') {
        localStorage.setItem(Dwnload_WP_Settings.localStorageItemId, id)
      }
    },

    /**
     * Gets the localStorage item.
     *
     * @returns {string}
     */
    getActiveTab: function () {
      let activeTab

      if (typeof(localStorage) !== 'undefined') {
        activeTab = localStorage.getItem(Dwnload_WP_Settings.localStorageItemId)
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
    },

    /**
     * @todo listen for save all forms submit button to trigger AJAX save all.
     */
    submitListener: function () {
      $(document.body).on('click', '.Dwnload_WP_Settings_Api__group input[type="submit"]', function () {
        //let group = Dwnload_WP_Settings.objects.group,
        //  form;
        //
        //form = group.is(':visible').find('form');
        //$(this).attr('form', form.attr('id'));
        //form.submit();
      }) //*/
    }
  }

  $(document).ready(Dwnload_WP_Settings.init())

}(jQuery))