;jQuery(document).on('app_ready', function(e, app) {
    var $ = jQuery,
        resultTemplatesCloner = null,
        questionsCloner = null,
        updateQuizResultsControls = function(itemsCount) {
            if (itemsCount > 0)
                $('#btnDeleteTemplates')
                    .attr('disabled', null)
                    .removeClass('disabled');
            else
                $('#btnDeleteTemplates')
                    .attr('disabled', 'disabled')
                    .addClass('disabled');
        },
        initQuizResultEditor = function(cloner, item) {
            var ctrlDescription = cloner.getControl('template_content', item),
                ctrlDescriptionId = ctrlDescription.attr('id');

            $('#' + ctrlDescriptionId).trumbowyg();
        },
        updateScoreRanges = function(cloner, item) {
            var ctrlEndScore = cloner.getControl('end_point', item);

            ctrlEndScore.on('input', function() {
                recalculateScoreRanges(cloner);
            });
        },
        recalculateScoreRanges = function(cloner) {
            var items = [],
                emptyItems = [];

            cloner.findClonerElements('.ari-cloner-template').each(function() {
                var item = $(this),
                    ctrlEndScore = cloner.getControl('end_point', item),
                    endScore = $.trim(ctrlEndScore.val());

                ctrlEndScore.removeClass('invalid');

                if (endScore.length == 0) {
                    emptyItems.push(item);
                    return ;
                }

                if (!/^[0-9]+$/.test(endScore)) {
                    ctrlStartScore = cloner.getControl('start_point', item.item);
                    ctrlStartScore.val('X');
                    ctrlEndScore.addClass('invalid');

                    return ;
                }

                endScore = parseInt(endScore, 10);

                items.push({
                    'endScore': endScore,

                    'item': item
                });
            });

            items.sort(function(a, b) {
                if (a.endScore < b.endScore)
                    return -1;
                else if (a.endScore > b.endScore)
                    return 1;

                return 0;
            });

            $.each(items, function(idx, item) {
                var startPoint = idx > 0 ? items[idx - 1].endScore + 0.5 : 0,
                    ctrlStartScore = cloner.getControl('start_point', item.item);

                ctrlStartScore.val(startPoint);
            });

            var maxScore = items.length > 0 ? items[items.length - 1].endScore + 0.5 : 0;
            $.each(emptyItems, function(idx, item) {
                cloner.getControl('start_point', item).val(maxScore);
            });
        },
        updateQuestionsControls = function(itemsCount) {
            if (itemsCount > 0)
                $('#btnDeleteQuestions')
                    .attr('disabled', null)
                    .removeClass('disabled');
            else
                $('#btnDeleteQuestions')
                    .attr('disabled', 'disabled')
                    .addClass('disabled');
        },
        updateQuestionsIndex = function(cloner) {
            cloner
                .findClonerElements('.ari-cloner-template')
                .each(function(idx) {
                    $(this).find('.question-index').html('' + (idx + 1));
                });
        },
        updateResultTemplateTitle = function(cloner, item) {
            var ctrlTitle = cloner.getControl('template_title', item);

            ctrlTitle.on('input', function() {
                var val = $.trim($(this).val());
                if (!val)
                    val = app.options.messages.untitled;

                item.find('.result-template-title').html(AppHelper.utils.stripTags(val));
            });

            ctrlTitle.trigger('input');
        },
        updateQuestionTitle = function(cloner, item) {
            var ctrlTitle = cloner.getControl('question_title', item);

            ctrlTitle.on('input', function() {
                var val = $(this).val();
                if (!val)
                    val = app.options.messages.untitled;

                item.find('.question-text').html(AppHelper.utils.stripTags(val));
            });

            ctrlTitle.trigger('input');

        },
        validate = function() {
            var questions = questionsCloner.getData(true);

            if (!questions || questions.length == 0) {
                AppHelper.alert(app.options.messages.noQuestionsWarning);
                return false;
            }

            var hasQuestions = false,
                hasEmptyQuestion = false,
                hasNonCorrectQuestion = false;

            for (var i = 0; i < questions.length; i++) {
                var question = questions[i],
                    question_title = $.trim(question.question_title),
                    image_id = parseInt(question.image_id, 10);

                if (question_title || image_id > 0) {
                    if (!question.answers || question.answers.length == 0) {
                        hasEmptyQuestion = true;
                    } else {
                        var containAnswer = false,
                            nonCorrectQuestion = true;

                        for (var j = 0; j < question.answers.length; j++) {
                            var answer = question.answers[j],
                                answer_image_id = parseInt(answer.image_id, 10),
                                answer_text = answer.answer_title,
                                isCorrect = answer.answer_correct;

                            if (answer_text || answer_image_id > 0) {
                                containAnswer = true;
                            }

                            if (isCorrect) {
                                nonCorrectQuestion = false;
                            }
                        }

                        if (nonCorrectQuestion)
                            hasNonCorrectQuestion = true;

                        if (containAnswer)
                            hasQuestions = true;
                        else
                            hasEmptyQuestion = true;
                    }
                } else {
                    hasEmptyQuestion = true;
                }

                if (hasQuestions && hasEmptyQuestion && hasNonCorrectQuestion)
                    break;
            }

            if (!hasQuestions) {
                AppHelper.alert(app.options.messages.emptyQuestionWarning);
                return false;
            }

            return true;
        };

    resultTemplatesCloner = $('#quiz_result_templates').ariCloner({
        'sortable': {
            'options': {
                'handle': '.sort-handle',

                'helper': 'clone',

                'items': '>.collapsible-container>.ari-cloner-template'
            }
        },

        'onBeforeAddItem': function() {
            this.getElement().find('.collapsible-header.active')
                .click()
                .stop(true, true)
                .siblings('.collapsible-body')
                .stop(true, true);
        },

        'onAddItem': function(item) {
            var ctrlTitle = this.getControl('template_title', item);
            ctrlTitle.focus();

            updateScoreRanges(this, item);
            initQuizResultEditor(this, item);
            updateResultTemplateTitle(this, item);

            item.find('.collapsible-header').not('.active').click();
        },

        'onInit': function() {
            var self = this;

            recalculateScoreRanges(this);
            updateQuizResultsControls(this.getItemsCount());

            var collapsibleContainer = this.findClonerElements('.collapsible-container');
            collapsibleContainer.addClass('collapsible').collapsible();
            collapsibleContainer.on('click', '.collapsible-header', function(e) {
                app.handleLazyLoadImages($(this).siblings('.collapsible-body'));
            });

            this.findClonerElements('.ari-cloner-template').each(function() {
                var item = $(this);

                updateScoreRanges(self, item);
                initQuizResultEditor(self, item);
                updateResultTemplateTitle(self, item);
            });

            if (app.options.isNew) {
                this.getElement().find('.collapsible-header').click();
            }
        },

        'onItemsChanged': function() {
            recalculateScoreRanges(this);
            updateQuizResultsControls(this.getItemsCount());
        }
    }, app.options.results || null);

    questionsCloner = $('#quiz_questions').ariCloner({
        'sortable': {
            'options': {
                'handle': '.sort-handle',

                'helper': 'clone',

                'items': '>.collapsible-container>.ari-cloner-template'
            }
        },

        'onBeforeAddItem': function() {
            this.getElement().find('.collapsible-header.active')
                .click()
                .stop(true, true)
                .siblings('.collapsible-body')
                .stop(true, true);
        },

        'onAddItem': function(item) {
            var ctrlTitle = this.getControl('question_title', item);
            ctrlTitle.focus();

            updateQuestionTitle(this, item);

            item.find('.collapsible-header').not('.active').click();
        },

        'onInit': function() {
            var self = this;

            updateQuestionsControls(this.getItemsCount());
            updateQuestionsIndex(this);

            var collapsibleContainer = this.findClonerElements('.collapsible-container');
            collapsibleContainer.addClass('collapsible').collapsible();
            collapsibleContainer.on('click', '.collapsible-header', function(e) {
                app.handleLazyLoadImages($(this).siblings('.collapsible-body'));
            });

            this.findClonerElements('.ari-cloner-template').each(function() {
                updateQuestionTitle(self, $(this));
            });

            if (app.options.isNew) {
                this.getElement().find('.collapsible-header').click();
            }
        },

        'onItemsChanged': function() {
            updateQuestionsControls(this.getItemsCount());
            updateQuestionsIndex(this);
        },

        'childClonersOptions': {
            'answers': {
                'onAddItem': function(item) {
                    var ctrlTitle = this.getControl('answer_title', item);
                    ctrlTitle.focus();
                },
                'scrollTo': {
                    'options': {
                        offset: {'top': '40px'}
                    }
                }
            }
        }
    }, app.options.questions || null);

    app.on('action', function(e, action) {
        if (action == 'save' || action == 'save_preview' || action == 'apply') {
            var resultTemplates = resultTemplatesCloner.getData(true),
                questions = questionsCloner.getData(true);

            var isValid = validate();

            if (isValid) {
                $('#hidQuizResultTemplates').val(JSON.stringify(resultTemplates));
                $('#hidQuestions').val(JSON.stringify(questions));
            } else {
                e.stopImmediatePropagation();
            }

            return isValid;
        }
    });

    // metabox
    $('#quizTrivia_link_addTemplate').on('click', function() {
        $('#quiz_result_templates').ariCloner().addItem();

        return false;
    });

    $('#quizTrivia_link_collapseAllTemplates').on('click', function() {
        $('#quiz_result_templates .collapsible-header').removeClass('active');
        $('#quiz_result_templates .collapsible-body').hide();

        return false;
    });

    $('#quizTrivia_link_expandAllTemplates').on('click', function() {
        $('#quiz_result_templates .collapsible-header').addClass('active');
        $('#quiz_result_templates .collapsible-body').show();

        return false;
    });

    $('#quizTrivia_link_addQuestion').on('click', function() {
        $('#quiz_questions').ariCloner().addItem();

        return false;
    });

    $('#quizTrivia_link_collapseAllQuestions').on('click', function() {
        $('#quiz_questions .collapsible-header').removeClass('active');
        $('#quiz_questions .collapsible-body').hide();

        return false;
    });

    $('#quizTrivia_link_expandAllQuestions').on('click', function() {
        $('#quiz_questions .collapsible-header').addClass('active');
        $('#quiz_questions .collapsible-body').show();

        return false;
    });

    AppHelper.hideLoading(app);
});