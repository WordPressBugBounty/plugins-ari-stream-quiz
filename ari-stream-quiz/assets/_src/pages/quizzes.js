;jQuery(document).on('app_ready', function(e, app) {
    if (app.options.preview) {
        var win = window.open(app.options.preview, '_blank');
        win.focus();
    };

    var $ = jQuery,
        init = function() {
            $('#tbxSearchText').on('keydown', function(e) {
                if (e.keyCode === 13) {
                    app.trigger('search');
                    return false;
                }
            });

            $('#btnQuizSearch').on('click', function() {
                app.trigger('search');
                return false;
            });

            $('#btnQuizSearchReset').on('click', function() {
                app.trigger('reset');
                return false;
            });

            $('.pagination', app.el).on('click', '.grid-page', function() {
                var pageNum = parseInt($(this).attr('data-page'), 10);

                if (pageNum >= 0) {
                    $('#hidQuizzesPageNum').val(pageNum);
                    app.trigger('page_change');
                }

                return false;
            });
            $('.go-to-page', app.el).on('change', function() {
                var pageNum = parseInt($(this).val(), 10);

                if (pageNum < 0)
                    return ;

                $('#hidQuizzesPageNum').val(pageNum);
                app.trigger('page_change');
            });

            var gridQuizzes = $('#gridQuizzes');
            gridQuizzes.on('click', '.sortable', function() {
                var $el = $(this),
                    sortColumn = $el.attr('data-sort-column'),
                    sortDir = $el.attr('data-sort-dir');

                if (!sortDir)
                    sortDir = 'ASC';
                else
                    sortDir = sortDir == 'ASC' ? 'DESC' : 'ASC';

                $('#hidQuizzesSortBy').val(sortColumn);
                $('#hidQuizzesSortDir').val(sortDir);

                app.trigger('sort');

                return false;
            });

			gridQuizzes.find('TBODY').off('click');
            gridQuizzes.on('click', '.toggle-row', function(e) {
                var tr = $(this).closest('TR');

                if (tr.hasClass('is-expanded')) {
                    tr.removeClass('is-expanded');
                } else {
                    tr.addClass('is-expanded');
                }

                e.stopImmediatePropagation();
                return false;
            });

            gridQuizzes.on('click', '.btn-quiz-delete', function() {
                var quizId = $(this).attr('data-quiz-id');

                AppHelper.confirm(app.options.messages.deleteConfirm, function() {
                    $('#hidQuizId').val(quizId);
                    app.trigger('delete');
                });

                return false;
            });

            gridQuizzes.on('click', '.btn-quiz-copy', function() {
                var quizId = $(this).attr('data-quiz-id');

                AppHelper.confirm(app.options.messages.copyConfirm, function() {
                    $('#hidQuizId').val(quizId);
                    app.trigger('copy');
                });

                return false;
            });

            var bulkActionsSelect = $('.bulk-actions-select', app.el);
            bulkActionsSelect.on('change', function() {
                bulkActionsSelect.val($(this).val());
            });

            $('.select-all-items', app.el).on('change', function() {
                gridQuizzes.find('.chk-quiz').prop('checked', $(this).is(':checked'));
            });

            $('.btn-bulk-apply', app.el).on('click', function() {
                var action = bulkActionsSelect.eq(0).val();

                if (!action)
                    return false;

                if (gridQuizzes.find('.chk-quiz:checked').length == 0) {
                    AppHelper.alert(app.options.messages.selectQuizzesWarning);
                    return false;
                }

                switch (action) {
                    case 'bulk_delete':
                        AppHelper.confirm(app.options.messages.bulkDeleteConfirm, function() {
                            app.trigger(action);
                        });
                        break;

                    case 'bulk_copy':
                        AppHelper.confirm(app.options.messages.bulkCopyConfirm, function() {
                            app.trigger(action);
                        });
                        break;

                    default:
                        app.trigger(action);
                        break;
                }

                return false;
            });

            var clipboard = new Clipboard('.asq-shortcode-btn-copy');
            clipboard.on('success', function(e) {
                Materialize.toast( app.options.messages.shortcodeCopied, 500 );

                e.clearSelection();
            });
            clipboard.on('error', function() {
                Materialize.toast( app.options.messages.shortcodeCopyFailed, 2000 );
            });
        };

    init();
});