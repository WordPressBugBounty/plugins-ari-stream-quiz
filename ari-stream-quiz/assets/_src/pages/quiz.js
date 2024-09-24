jQuery(function($) {
    $('#metaBox').pushpin({
        offset: $('#metaBox').offset().top
    });

    $(document).on('click', '.ari-media-library,.ari-wp-image-holder IMG', function(e) {
        var target = $(e.target);

        var custom_uploader = wp.media.frames.file_frame = wp.media({
            title: target.data('wpmedia-title'),
            button: {
                text: target.data('wpmedia-button')
            },
            multiple: false
        });

        custom_uploader.on('select', function () {
            var attachment = custom_uploader.state().get('selection').first().toJSON();

            var container = target.closest('.ari-wp-image-container'),
                imgId = container.find('.ari-wp-image-id'),
                imgHolder = container.find('.ari-wp-image-holder'),
                img = imgHolder.find('img');

            if (img.length == 0) {
                img = $('<img />').appendTo(imgHolder);
            }

            img.attr('src', attachment.url);
            imgId.val(attachment.id);

            container.addClass('has-image');
        });

        custom_uploader.open();

        return false;
    });

    $(document).on('click', '.ari-media-library-remove', function(e) {
        var target = $(e.target),
            container = target.closest('.ari-wp-image-container'),
            imgId = container.find('.ari-wp-image-id');

        container.find('.ari-wp-image-holder img').remove();
        container.removeClass('has-image');

        imgId.val('');

        return false;
    });
});
;jQuery(document).on('app_ready', function(e, app) {
    var imageLoaded = function(img, status) {
        status = status || 'load';

        if (status == 'error')
            return ;

        img.removeAttr('data-url');
        img.css('opacity', 0).animate({'opacity': 1}, 400);

        var container = img.closest('.ari-lazy-load');
        container.removeClass('ari-lazy-load');
    };

    app.handleLazyLoadImages = function(container) {
        if (container.data('lazyLoaded'))
            return ;

        container.data('lazyLoaded', true);

        container.find('.ari-lazy-load IMG').each(function() {
            var $img = $(this),
                url = $img.attr('data-url');

            $img.attr('src', url);

            if (this.complete) {
                imageLoaded($img);
            } else {
                $img.one('load', function () {
                    imageLoaded($img);
                }).one('error', function() {
                    imageLoaded($img, 'error');
                });
            }
        });
    };

    var $ = jQuery,
        handleTabs = function() {
            $('#quiz_settings_tabs .active').each(function() {
                var container = $('#quizContainer'),
                    containerClass = $(this).data('container-class'),
                    currentContainerClass = container.data('container-class');

                if (currentContainerClass)
                    container.removeClass(currentContainerClass);

                if (containerClass) {
                    container.addClass(containerClass);
                    container.data('container-class', containerClass);
                } else {
                    container.data('container-class', null);
                }
            });
        },
        validate = function() {
            var tbxQuizTitle = $('#tbxQuizTitle'),
                quizTitle = $.trim(tbxQuizTitle.val());

            if (!quizTitle) {
                tbxQuizTitle.addClass('invalid');
                tbxQuizTitle.focus();

                AppHelper.alert(app.options.messages.emptyTitleWarning);

                return false;
            } else {
                tbxQuizTitle.removeClass('invalid');
            }

            return true;
        },
        init = function() {
            $('#tbxQuizDescription').trumbowyg();

            $('select.listbox', app.el).material_select();

            $('.child-controls-inline', app.el).each(function() {
                var childControl = $('[data-ref-id]', this),
                    mainControl = $('#' + childControl.attr('data-ref-id')),
                    isActive = mainControl.is(':checked'),
                    handler = function(isActive) {
                        if (isActive) {
                            childControl.removeClass('disabled');
                            childControl.find('INPUT,SELECT').attr('disabled', null);
                        } else {
                            childControl.addClass('disabled');
                            childControl.find('INPUT,SELECT').attr('disabled', true);
                        }
                    };

                mainControl.on('change', function() {
                    handler($(this).is(':checked'));
                });
                childControl.on('click', function() {
                    if ($(this).hasClass('disabled')) {
                        mainControl.click();
                    }
                });

                handler(isActive);
            });

            $('.block-switcher', app.el).each(function() {
                var chk = $(this),
                    containerEl = $('#' + chk.attr('data-ref-id')),
                    isActive = chk.is(':checked');

                if (!isActive)
                    containerEl.hide();

                chk.on('change', function() {
                    if ($(this).is(':checked')) {
                        containerEl.slideDown();
                    } else {
                        //containerEl.hide();
                        containerEl.slideUp();
                    }
                });
            });

            var quizImage = app.options['quizImage'];
            if (quizImage && quizImage.url) {
                var el = $('#hidQuiImageId'),
                    container = el.closest('.ari-wp-image-container'),
                    imgHolder = container.find('.ari-wp-image-holder'),
                    img = $('<img />')
                        .appendTo(imgHolder)
                        .attr('src', quizImage.url);

                container.addClass('has-image');
            };

            $('#ddlMailchimpListId').ariSmartDropDown({
                ajaxUrl: app.options.ajaxUrl,

                ajaxData: {
                    'ctrl': 'quiz_get-mailchimp-lists'
                },

                messages: {
                    loading: app.options.messages.loading
                },

                onChange: function(listId, listName) {
                    $('#hidMailchimpListName').val(listId ? listName : '');
                }
            });

            $('#mailchimpListRefresh').on('click', function() {
                $('#ddlMailchimpListId').ariSmartDropDown().refresh();

                return false;
            });

            $('#ddlMailerLiteListId').ariSmartDropDown({
                ajaxUrl: app.options.ajaxUrl,

                ajaxData: {
                    'ctrl': 'quiz_get-mailerlite-lists'
                },

                messages: {
                    loading: app.options.messages.loading
                },

                onChange: function(listId, listName) {
                    $('#hidMailerLiteListName').val(listId ? listName : '');
                }
            });

            $('#mailerLiteListRefresh').on('click', function() {
                $('#ddlMailerLiteListId').ariSmartDropDown().refresh();

                return false;
            });
        };

    init();

    $('#quiz_settings_tabs').tabs({
        'onShow': function () {
            handleTabs();
        }
    });
    handleTabs();

    app.on('action', function(e, action) {
        if (action == 'save' || action == 'save_preview' || action == 'apply') {
            var isValid = validate();

            if (!isValid) {
                e.stopImmediatePropagation();
                return false;
            }
        } else if (action == 'cancel') {
            AppHelper.confirm(app.options.messages.cancelWarning, function() {
                app.trigger('cancel', true);
            });

            e.stopImmediatePropagation();
            return false;
        }
    });
});