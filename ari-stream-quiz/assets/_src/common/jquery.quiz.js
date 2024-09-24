;(function($, undefined) {
    if (undefined !== $.fn['ariStreamQuiz'])
        return ;

    var Base64 = {
        _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

        decode: function(input) {
            var output = '';
            var chr1, chr2, chr3;
            var enc1, enc2, enc3, enc4;
            var i = 0;

            input = input.replace(/[^A-Za-z0-9\+\/\=]/g, '');

            while (i < input.length) {
                enc1 = this._keyStr.indexOf(input.charAt(i++));
                enc2 = this._keyStr.indexOf(input.charAt(i++));
                enc3 = this._keyStr.indexOf(input.charAt(i++));
                enc4 = this._keyStr.indexOf(input.charAt(i++));

                chr1 = (enc1 << 2) | (enc2 >> 4);
                chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                chr3 = ((enc3 & 3) << 6) | enc4;

                output = output + String.fromCharCode(chr1);

                if (enc3 != 64) {
                    output = output + String.fromCharCode(chr2);
                }
                if (enc4 != 64) {
                    output = output + String.fromCharCode(chr3);
                }
            };

            output = Base64._utf8_decode(output);

            return output;
        },

        _utf8_decode: function(utftext) {
            var string = '';
            var i = 0;
            var c = c1 = c2 = 0;

            while (i < utftext.length) {
                c = utftext.charCodeAt(i);

                if (c < 128) {
                    string += String.fromCharCode(c);
                    i++;
                }
                else if ((c > 191) && (c < 224)) {
                    c2 = utftext.charCodeAt(i + 1);
                    string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                    i += 2;
                }
                else {
                    c2 = utftext.charCodeAt(i + 1);
                    c3 = utftext.charCodeAt(i + 2);
                    string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                    i += 3;
                }
            };

            return string;
        }
    };

    var FACEBOOK_API_LOADER = {
        initialized: false,

        isSDKLoaded: false,

        settings: {
            cookie: true,

            status: true,

            xfbml: true,

            version: 'v2.9'
        },

        init: function(settings) {
            if (this.initialized)
                return ;

            this.settings = $.extend(
                this.settings,
                settings || {}
            );

            var self = this;
            if ($.isFunction(window['fbAsyncInit'])) {
                if (window.fbAsyncInit.hasRun === true) {
                    this.SDKLoaded();
                } else {
                    var oldHandler = window.fbAsyncInit;

                    window.fbAsyncInit = function() {
                        oldHandler();

                        self.SDKLoaded();
                    }
                }
            } else if (window['FB'] !== undefined) {
                setTimeout(function() {
                    self.SDKLoaded();
                }, 10);
            } else {
                window.fbAsyncInit = function() {
                    self.SDKLoaded();
                }
            };

            this.initialized = true;
        },

        SDKLoaded: function() {
            FB.init(this.settings);

            this.isSDKLoaded = true;

            this.triggerCompleteEvent();
        },

        triggerCompleteEvent: function() {
            $(document).trigger('fb_api_loaded:stream_quiz');
        },

        onComplete: function(handler, notExecuteIfFired) {
            notExecuteIfFired = notExecuteIfFired || false;

            if (this.isSDKLoaded) {
                if (!notExecuteIfFired)
                    handler();
            } else {
                $(document).on('fb_api_loaded:stream_quiz', handler);
            }
        }
    };

    var EXIT_WARNING_STATUS = {
            disable: 0,

            enable: 1
        },
        EXIT_WARNING_HANDLER = {
            isEventHandlerAttached: false,

            disabled: false,

            items: {},

            attach: function(id, status) {
                if (undefined === status)
                    status = EXIT_WARNING_STATUS.enable;

                this.items[id] = {
                    status: status
                };

                if (!this.isEventHandlerAttached) {
                    var self = this;

                    window.onbeforeunload = function() {
                        if (!self.disabled && self.hasActiveItem())
                            return ARI_STREAM_QUIZ_L10N.warningOnExit || 'The quiz is not completed, do you want to leave the page?';
                        else
                            return null;
                    };
                }
            },

            detach: function(id) {
                this.items[id] = null;
                delete this.items[id];
            },

            disable: function() {
                this.disabled = true;
            },

            enable: function() {
                this.disabled = false;
            },

            disableItem: function(id) {
                this.changeItemStatus(id, EXIT_WARNING_STATUS.disable);
            },

            enableItem: function(id) {
                this.changeItemStatus(id, EXIT_WARNING_STATUS.enable);
            },

            changeItemStatus: function(id, status, attachNotExist) {
                attachNotExist = attachNotExist || false;

                if (!this.items[id] && attachNotExist) {
                    this.attach(id, status);
                }

                if (this.items[id])
                    this.items[id]['status'] = status;
            },

            hasActiveItem: function() {
                for (var id in this.items) {
                    var item = this.items[id];

                    if (item && item.status == EXIT_WARNING_STATUS.enable)
                        return true;
                }

                return false;
            }
        };

    var CLASSES = {
            'loading': 'quiz-loading',

            'lazy_load': 'lazy-load',

            'start_quiz_btn': 'button-start-quiz',

            'question_container': 'quiz-question',

            'answers_container': 'quiz-question-answers',

            'answer_container': 'quiz-question-answer',

            'answer_image_container': 'quiz-question-answer-image',

            'answer_selected': 'quiz-question-answer-selected',

            'question_answered': 'question-answered',

            'answer_control': 'quiz-question-answer-ctrl',

            'answer_control_label': 'quiz-question-answer-ctrl-lbl',

            'next_page_btn': 'button-next-page',

            'collect_data_btn': 'button-collect-data',

            'skip_collect_data_btn': 'button-skip-collect-data',

            'page': 'quiz-page',

            'page_current': 'current',

            'page_completed': 'quiz-page-completed',

            'result_template': 'quiz-result-template',

            'result_wrapper': 'quiz-result-wrapper',

            'question_result': 'quiz-question-result',

            'user_data': 'quiz-user-data',

            'data_loaded': 'data-loaded',

            'correct_answer': 'quiz-question-answer-correct',

            'wrong_answer': 'quiz-question-answer-wrong',

            'question_correct': 'quiz-question-correct',

            'question_wrong': 'quiz-question-wrong',

            'question_completed': 'question-completed'
        },
        ATTRS = {
            'question_id': 'data-question-id',

            'page': 'data-page',

            'share_url': 'data-share-url',

            'share_disable_modal': 'data-share-disable-modal',

            'user_data_key': 'data-key',

            'img_src': 'data-src'
        },
        DATA_KEYS = {
            'items_in_progress': 'items-in-progress',

            'share_title': 'share-title',

            'share_description': 'share-description',

            'share_image': 'share-image'
        },
        QUIZ_TYPE = {
            'trivia': 'TRIVIA'
        },
        VIEWS = {
            'quiz_intro': 'quiz-intro',

            'quiz_session': 'quiz-session',

            'quiz_user_data': 'quiz-user-data',

            'quiz_results': 'quiz-results'
        },
        EVENTPOSTFIX = '.aristreamquiz',
        QUIZZES = {},
        ID_COUNTER = 0,
        EXTRA_OFFSET = -20,
        generateId = function(prefix) {
            prefix = prefix || 'quiz_';

            return prefix + (ID_COUNTER++);
        },
        format = function(str, opt) {
            return str.replace(
                /\{\{([^}]+)}}/g,
                function($0, $1) {
                    return opt && undefined !== opt[$1] ? opt[$1] : '';
                }
            );
        },
        extractText = function(html) {
            return $('<div>' + html + '</div>').text();
        },
        openPopup = function(href, settings) {
            if (settings === undefined)
                settings = 'width=600,height=350,top=50,left=250,resizable=yes,menubar=no,status=no,toolbar=no,scrollbars=no';

            return window.open(href, '', settings);
        },
        quoteAttr = function(s, preserveCR) {
            preserveCR = preserveCR ? '&#13;' : '\n';
            return ('' + s) /* Forces the conversion to string. */
                .replace(/&/g, '&amp;') /* This MUST be the 1st replacement. */
                .replace(/'/g, '&apos;') /* The 4 other predefined entities, required. */
                .replace(/"/g, '&quot;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                /*
                 You may add other replacements here for HTML only
                 (but it's not necessary).
                 Or for XML, only if the named entities are defined in its DTD.
                 */
                .replace(/\r\n/g, preserveCR) /* Must be before the next replacement. */
                .replace(/[\r\n]/g, preserveCR);
        };

    var Quiz = function(el, options) {
        this.el = $(el);
        this.userAnswers = {};

        options = options || {};

        if (options.data) {
            var data;
            try {
                data = eval('(function(){return ' + Base64.decode(options.data) + '})()');
            } catch (e) {
                data = null;
            }
            options.data = data;
        } else
            options.data = null;

        this.options = $.extend(
            true,
            {},
            $.fn.ariStreamQuiz.defaults,
            options
        );

        this.init();
    };

    Quiz.prototype = {
        el: null,

        completedQuestionCount: 0,

        questionCount: 0,

        pageCount: 0,

        completedPages: 0,

        currentPage: 0,

        userAnswers: null,

        options: null,

        loadingCount: 0,

        constructor: Quiz,

        getCurrentView: function() {
            return this.el.data('view') || VIEWS.quiz_intro;
        },

        changeView: function(view) {
            var currentView = this.el.data('view');

            if (currentView && currentView != view) {
                var currentViewClass = 'view-' + currentView;
                this.el.removeClass(currentViewClass);
            }

            var newViewClass = 'view-' + view;
            this.el.data('view', view);
            this.el.addClass(newViewClass);

            if (this.options.warningOnExit) {
                var id = this.el.attr('id');
                if (view == VIEWS.quiz_results) {
                    EXIT_WARNING_HANDLER.detach(id);
                } else if (view != VIEWS.quiz_intro) {
                    var status = (view == VIEWS.quiz_results || view == VIEWS.quiz_intro) ? EXIT_WARNING_STATUS.disable : EXIT_WARNING_STATUS.enable;
                    EXIT_WARNING_HANDLER.changeItemStatus(id, status, true);
                }
            }
        },

        init: function() {
            var options = this.options,
                self = this;

            this.pageCount = options.data.pageCount;
            this.questionCount = options.data.questionCount;

            if (options.data.facebook.enabled) {
                FACEBOOK_API_LOADER.init(options.data.facebook.settings);

                this.prepareFacebookElements();
            };

            if (!options.data.startImmediately) {
                this.changeView(VIEWS.quiz_intro);
                this.el.one('click' + EVENTPOSTFIX, '.' + CLASSES.start_quiz_btn, function(e) {
                    self.startQuiz();

                    return false;
                });
            } else {
                self.startQuiz();
            };

            this.el.on('click' + EVENTPOSTFIX, '.' + CLASSES.answer_container, function(e) {
                var target = $(e.target);
                if (target.hasClass(CLASSES.answer_control)) {
                    if (target.is(':disabled'))
                        return false;

                    var checked = !e.target.checked,
                        res = self.answerSelected(target.val(), target.attr(ATTRS.question_id));

                    if (res === false) {
                        e.stopImmediatePropagation();
                        return false;
                    }

                    if (target.is(':disabled')) {
                        target.prop('checked', checked);
                    }

                    return;
                }

                e.stopImmediatePropagation();
                $(this).find('.' + CLASSES.answer_control).trigger('click');
                return false;
            });

            if (!options.lazyLoad)
                this.prepareQuestionsSize();

            $(window).on('resize orientationchange', function() {
                self.normalizeAnswersSize();
            });

            this.normalizeAnswersSize();
        },

        prepareQuestionsSize: function(el) {
            el = el || this.el;

            var options = this.options,
                self = this;

            $('.' + CLASSES.question_container, el).each(function() {
                var $questionContainer = $(this),
                    questionId = parseInt($questionContainer.attr(ATTRS.question_id), 10),
                    $answersContainer = $('#'+ options.prefix + '_answers_' + questionId),
                    $answersImgList = $('IMG', $answersContainer);

                if ($answersImgList.length == 0) {
                    $answersContainer.addClass(CLASSES.data_loaded);
                    $answersContainer.trigger('data_loaded');
                } else {
                    $answersContainer.data(DATA_KEYS.items_in_progress, $answersImgList.length);

                    $answersImgList.each(function() {
                        var callback = function(img, status) {
                            var $img = $(img),
                                itemsInProgress = $answersContainer.data(DATA_KEYS.items_in_progress);
                            $img.closest('.' + CLASSES.answer_image_container).addClass(CLASSES.data_loaded);

                            --itemsInProgress;
                            $answersContainer.data(DATA_KEYS.items_in_progress, itemsInProgress);

                            if (itemsInProgress == 0) {
                                $answersContainer.addClass(CLASSES.data_loaded);
                                $answersContainer.trigger('data_loaded');
                            }
                        };

                        if (this.complete) {
                            callback.call(self, this, 'load');
                        } else {
                            $(this).one('load', function () {
                                callback.call(self, this, 'load');
                            }).one('error', function() {
                                callback.call(self, this, 'error');
                            });
                        }
                    });
                }
            });
        },

        normalizeAnswersSize: function() {
            var self = this;

            this.el.find('.' + CLASSES.answers_container + ':visible').each(function() {
                var $answersContainer = $(this);

                if ($answersContainer.hasClass(CLASSES.data_loaded)) {
                    self.normalizeAnswersSizeImmediately($answersContainer);
                } else {
                    $answersContainer.off('data_loaded.normalize').one('data_loaded.normalize', function() {
                        self.normalizeAnswersSizeImmediately($answersContainer);
                    });
                }
            });
        },

        normalizeAnswersSizeImmediately: function(answersContainer) {
            if (!answersContainer.is(':visible'))
                return ;

            var answerList = $('.' + CLASSES.answer_container, answersContainer),
                answerImageContainerList = $('.' + CLASSES.answer_image_container, answersContainer),
                maxImgHeight = 0,
                maxAnswerHeight = 0;

            answerList.css('height', 'auto');
            answerImageContainerList.css('height', 'auto');

            answerList.each(function() {
                var itemHeight = $(this).outerHeight();

                if (maxAnswerHeight < itemHeight)
                    maxAnswerHeight = itemHeight;
            });

            answerImageContainerList.each(function() {
                var itemHeight = $(this).outerHeight();

                if (maxImgHeight < itemHeight)
                    maxImgHeight = itemHeight;
            });

            answerList.css('height', maxAnswerHeight);
            answerImageContainerList.css('height', maxImgHeight);
        },

        prepareFacebookElements: function() {
            var self = this,
                resultFacebookShareBtn = $('.quiz-result-share-buttons .button-share.button-facebook', '#' + this.options.prefix + '_result'),
                forceShareHandler = function() {
                    if (self.options.data.collectData) {
                        self.showUserDataForm();
                    } else {
                        self.showResults();
                    }
                };

            // init fallback
            resultFacebookShareBtn.on('click.facebook', function() {
                openPopup($(this).attr('href'));

                return false;
            });

            if (self.options.data.facebook.settings.appId) {
                FACEBOOK_API_LOADER.onComplete(function() {
                    resultFacebookShareBtn.off('click.facebook').on('click.facebook', function(e) {
                        var $btn = $(this),
                            data = self.options.data,
                            share = data.share,
                            title = extractText($btn.data(DATA_KEYS.share_title) || ''),
                            imageUrl = $btn.data(DATA_KEYS.share_image) || '',
                            fbOptions = {
                                method: 'feed',
                                link: share.url,
                                quote: title,
                                name: title,
                                caption: share.url,
                                description: extractText($btn.data(DATA_KEYS.share_description) || '')
                            };

                        if (imageUrl)
                            fbOptions['picture'] = imageUrl;

                        FB.ui(fbOptions);

                        return false;
                    });
                });
            }
        },

        startQuiz: function() {
            this.changeView(VIEWS.quiz_session);

            var currentPageEl = this.currentPageEl();
            currentPageEl.addClass(CLASSES.page_current);

            var hasLazyLoadingElements = false;
            if (this.options.lazyLoad) {
                if (this.hasLazyLoadElements(currentPageEl)) {
                    hasLazyLoadingElements = true;

                    var self = this;

                    this.showLoading();
                    this.processLazyLoadElements(currentPageEl, function() {
                        self.hideLoading();
                        self.prepareQuestionsSize(currentPageEl);
                        setTimeout(function() {
                            self.normalizeAnswersSize();
                        }, 10);
                    });
                }
            } else {
                this.normalizeAnswersSize();
            };

            if (!hasLazyLoadingElements)
                this.hideLoading(true);

            if (this.options.smartScroll)
                this.scrollTo(currentPageEl);
        },

        hasLazyLoadElements: function(el) {
            return $(el).find('.' + CLASSES.lazy_load).length > 0;
        },

        processLazyLoadElements: function(el, callback) {
            var imgList = $('IMG.' + CLASSES.lazy_load, el);
            if (imgList.length == 0) {
                if (callback)
                    callback();

                return ;
            }

            var self = this;

            el.data(DATA_KEYS.items_in_progress, imgList.length);

            imgList.each(function() {
                var $img = $(this),
                    src = $img.attr(ATTRS.img_src);

                $img.attr('src', src);
                $img.removeClass(CLASSES.lazy_load);
            });

            imgList.each(function() {
                var imgLoadCallback = function(img, status) {
                    var $img = $(img),
                        itemsInProgress = el.data(DATA_KEYS.items_in_progress);

                    --itemsInProgress;
                    el.data(DATA_KEYS.items_in_progress, itemsInProgress);

                    if (itemsInProgress == 0) {
                        if (callback)
                            callback();
                    }
                };

                if (this.complete) {
                    imgLoadCallback.call(self, this, 'load');
                } else {
                    $(this).one('load', function () {
                        imgLoadCallback.call(self, this, 'load');
                    }).one('error', function() {
                        imgLoadCallback.call(self, this, 'error');
                    });
                }
            });
        },

        showLoading: function() {
            ++this.loadingCount;
            this.el.addClass(CLASSES.loading);
        },

        hideLoading: function(force) {
            force = force || false;

            if (force)
                this.loadingCount = 0;
            else
                --this.loadingCount;

            if (this.loadingCount < 1) {
                this.el.removeClass(CLASSES.loading);
            }
        },

        isQuizCompleted: function() {
            return this.completedQuestionCount == this.questionCount;
        },

        isQuestionAnswered: function(questionId) {
            return undefined !== this.userAnswers[questionId];
        },

        isPageCompleted: function() {
            return this.currentPageEl().find('.' + CLASSES.question_container + ':not(.' + CLASSES.question_answered + ')').length == 0;
        },

        showNextPage: function() {
            var currentPageEl = this.currentPageEl(),
                nextPage = currentPageEl.next('.' + CLASSES.page + ':not(.' + CLASSES.page_completed + ')');

            if (nextPage.length == 0) {
                nextPage = currentPageEl.prevAll('.' + CLASSES.page + ':not(.' + CLASSES.page_completed + ')').last();

                if (nextPage.length == 0) {
                    nextPage = currentPageEl.next('.' + CLASSES.page);
                    if (nextPage.length == 0) {
                        nextPage = currentPageEl.prevAll('.' + CLASSES.page).last();

                        if (nextPage.length == 0)
                            nextPage = null;
                    }
                }
            }

            if (!nextPage)
                return ;

            var nextPageNum = parseInt(nextPage.attr(ATTRS.page), 10);
            this.currentPage = nextPageNum;

            currentPageEl.removeClass(CLASSES.page_current);
            nextPage.addClass(CLASSES.page_current);

            if (this.options.lazyLoad) {
                if (this.hasLazyLoadElements(nextPage)) {
                    var self = this;

                    this.showLoading();
                    this.processLazyLoadElements(nextPage, function() {
                        self.hideLoading();
                        self.prepareQuestionsSize(nextPage);
                        setTimeout(function() {
                            self.normalizeAnswersSize();
                        }, 10);
                    });
                }
            } else {
                this.normalizeAnswersSize();
            };

            if (this.options.smartScroll)
                this.scrollTo('#' + this.options.prefix + '_top'/*this.currentPageEl()*/);
        },

        nextQuestionEl: function(questionId) {
            return $('#' + this.options.prefix + '_question_' + questionId).next('.' + CLASSES.question_container);
        },

        currentPageEl: function() {
            return $('#' + this.options.prefix + '_page_' + this.currentPage);
        },

        getQuestionById: function(questionId, pageNum) {
            if (pageNum === undefined)
                pageNum = -1;

            var pages = this.options.data.pages,
                question = null;

            if (pageNum > -1) {
                if (pages.length > pageNum && undefined !== pages[pageNum].questions[questionId])
                    question = pages[pageNum].questions[questionId];
            } else {
                for (var i = 0; i < pages.length; i++) {
                    var page = pages[i];

                    if (undefined !== page.questions[questionId]) {
                        question = page.questions[questionId];
                        break;
                    }
                }
            }

            return question;
        },

        showQuestionResult: function(questionId, pageNum) {
            if (QUIZ_TYPE.trivia != this.options.data.quizType)
                return ;

            var currentQuestion = this.getQuestionById(questionId, pageNum);
            if (!currentQuestion)
                return ;

            var answerId = undefined !== this.userAnswers[questionId] ? this.userAnswers[questionId] : -1,
                isCorrect = undefined !== currentQuestion.answers[answerId] ? currentQuestion.answers[answerId].correct : false,
                questionStatusContainer = $('#' + this.options.prefix + '_question_status_' + questionId),
                questionResult = $('.' + CLASSES.question_result, questionStatusContainer);

            for (var id in currentQuestion.answers) {
                var answer = currentQuestion.answers[id],
                    answerContainer = $('#' + this.options.prefix + '_answercontainer_' + id);

                if (answer.correct) {
                    answerContainer.addClass(CLASSES.correct_answer);
                } else if (id == answerId) {
                    answerContainer.addClass(CLASSES.wrong_answer);
                }
            }

            questionResult.html(isCorrect ? '<i class="quiz-icon-correct"></i>' + this.options.messages.correct : '<i class="quiz-icon-wrong"></i>' + this.options.messages.wrong);

            questionStatusContainer.addClass(isCorrect ? CLASSES.question_correct : CLASSES.question_wrong);

            questionStatusContainer.show();
        },

        completeQuestion: function(questionId) {
            var questionContainer = $('#' + this.options.prefix + '_question_' + questionId);

            questionContainer
                .addClass(CLASSES.question_completed)
                .find('.' + CLASSES.answer_control).each(function () {
                    $(this).attr('disabled', 'disabled');
                });
        },

        completeAllQuestions: function() {
            for (var i = 0; i < this.options.data.pages.length; i++) {
                var page = this.options.data.pages[i];

                for (var questionId in page.questions) {
                    this.completeQuestion(questionId);
                }
            }
        },

        answerSelected: function(answerId, questionId) {
            var self = this,
                isTriviaQuiz = true,
                isFirstAttempt = !this.isQuestionAnswered(questionId),
                lockoutAnswers = this.options.data.lockoutAnswers;

            answerId = parseInt(answerId, 10);
            questionId = parseInt(questionId, 10);

            if (isFirstAttempt) {
                var questionContainer = $('#' + this.options.prefix + '_question_' + questionId);
                questionContainer.addClass(CLASSES.question_answered);
                ++this.completedQuestionCount;

                if (lockoutAnswers)
                    self.completeQuestion(questionId);
            } else {
                if (!lockoutAnswers) {
                    var prevAnswerId = this.userAnswers[questionId];

                    $('#' + this.options.prefix + '_answercontainer_' + prevAnswerId).removeClass(CLASSES.answer_selected);
                }
            }

            if (lockoutAnswers && !isFirstAttempt)
                return ;

            $('#' + this.options.prefix + '_answercontainer_' + answerId).addClass(CLASSES.answer_selected);

            this.userAnswers[questionId] = answerId;

            var scrollToEl = null;
            // show result and explanation for trivia quizzes
            if ('immediately' == this.options.data.showResults && lockoutAnswers) {
                this.showQuestionResult(questionId, this.currentPage);

                scrollToEl = $('#' + this.options.prefix + '_question_status_' + questionId);
            }

            if (this.isQuizCompleted()) {
                ++this.completedPages;
                this.quizComplete();
                if (this.options.data.collectData) {
                    this.showUserDataForm();
                } else {
                    this.showResults();
                }

                return ;
            }

            if (this.options.smartScroll)
                this.scrollTo(scrollToEl ? scrollToEl : this.nextQuestionEl(questionId));
        },

        quizComplete: function() {
            var self = this;

            this.el.off('click' + EVENTPOSTFIX);

            setTimeout(function() {
                self.completeAllQuestions();
            }, 10);
        },

        showUserDataForm: function() {
            this.changeView(VIEWS.quiz_user_data);

            var self = this,
                userDataFormEl = $('#' + this.options.prefix + '_user_data');

            $('.' + CLASSES.collect_data_btn, userDataFormEl).on('click' + EVENTPOSTFIX, function() {
                if (self.options.userDataValidate) {
                    if (!self.options.userDataValidate.call(self))
                        return false;
                };

                var btn = $(this),
                    userData = self.collectUserData();

                btn.off('click' + EVENTPOSTFIX);

                if (!self.options.data.processUserData || !userData['email']) {
                    self.showResults();
                    return false;
                };

                var resultData = self.getResultData();

                userData['quiz'] = resultData['quiz'];
                userData['result'] = resultData['title'];

                var data = {
                    'ctrl': 'quiz-session_collect-data',

                    'id': self.options.data.quizId,

                    'user_data': JSON.stringify(userData)
                };

                $.ajax({
                    type: 'POST',

                    url: self.options.ajaxUrl,

                    data: data,

                    dataType: 'json'
                });

                self.showResults();
            });

            if (this.options.data.collectDataOptional) {
                $('.' + CLASSES.skip_collect_data_btn, userDataFormEl).on('click' + EVENTPOSTFIX, function() {
                    $('.' + CLASSES.collect_data_btn, userDataFormEl).off('click' + EVENTPOSTFIX);
                    $(this).off('click' + EVENTPOSTFIX);

                    self.showResults();

                    return false;
                });
            };

            if (this.options.smartScroll)
                this.scrollTo(userDataFormEl);
        },

        showResults: function() {
            this.changeView(VIEWS.quiz_results);

            if ('on_complete' == this.options.data.showResults || (!this.options.data.lockoutAnswers && 'immediately' == this.options.data.showResults)) {
                for (var i = 0; i < this.options.data.pages.length; i++) {
                    var page = this.options.data.pages[i];
                    for (var questionId in page.questions) {
                        this.showQuestionResult(questionId, i);
                    }
                }
            };

            this.normalizeAnswersSize();

            var resultEl = $('#' + this.options.prefix + '_result'),
                resultTemplate = $('#' + this.options.prefix + '_result_template').clone(true).attr('id', null).html(),
                resultData = this.getResultData();

            var resultContent = format(resultTemplate, resultData);

            this.prepareResultShareButtons(resultData);

            resultEl.find('.' + CLASSES.result_wrapper).append(resultContent);
            resultEl.show();

            if (this.options.smartScroll)
                this.scrollTo(resultEl);
        },

        prepareResultShareButtons: function(resultData) {
            $('.quiz-result-share-buttons .button-share', '#' + this.options.prefix + '_result').each(function() {
                var $btn = $(this),
                    href = $btn.attr(ATTRS.share_url),
                    dataTitle = $btn.data(DATA_KEYS.share_title) || '',
                    dataDescription = $btn.data(DATA_KEYS.share_description) || '';

                if (dataTitle) {
                    dataTitle = format(dataTitle, resultData);
                    $btn.data(DATA_KEYS.share_title, dataTitle);
                }

                if (dataDescription) {
                    dataDescription = format(dataDescription, resultData);
                    $btn.data(DATA_KEYS.share_description, dataDescription);
                }

                if (resultData['image_url']) {
                    $btn.data(DATA_KEYS.share_image, resultData['image_url']);
                }

                if (!href)
                    return ;

                var data = $.extend({}, resultData);
                data['item_title'] = dataTitle;
                data['item_content'] = dataDescription;

                for (var key in data) {
                    data[key] = encodeURIComponent(data[key]);
                };

                var disableModalWindow = !!$btn.attr(ATTRS.share_disable_modal);

                href = format(href, data);
                if (!disableModalWindow) {
                    $btn.on('click', function() {
                        openPopup(href);

                        return false;
                    });
                };

                $btn.attr('href', href);
            });
        },

        collectUserData: function() {
            var userDataFormEl = $('#' + this.options.prefix + '_user_data'),
                data = {};

            $('SELECT,INPUT,TEXTAREA', userDataFormEl).each(function() {
                var $this = $(this),
                    id = $this.attr(ATTRS.user_data_key),
                    val = $this.val();

                if (!id)
                    return ;

                data[id] = val;
            });

            return data;
        },

        getResultData: function() {
            var resultData = this.getResultData_trivia();

            if (resultData === null)
                resultData = {};

            resultData.image = '';
            if (resultData) {
                if (resultData.image_url)
                    resultData.image = '<img src="' + resultData.image_url + '" />';

                if (resultData.image_description) {
                    resultData.image_credit = quoteAttr(resultData.image_description);
                    resultData.image_class = 'has-credit';
                }
            }

            resultData = $.extend(resultData || {}, {
                quiz: this.options.data.share.title,

                url: this.options.data.share.url
            });

            return resultData;
        },

        getResultData_trivia: function() {
            var userScore = 0,
                data = this.options.data,
                resultTemplates = data.resultTemplates;

            for (var i = 0; i < data.pages.length; i++) {
                var page = data.pages[i];

                for (var questionId in page.questions) {
                    if (undefined === this.userAnswers[questionId])
                        continue ;

                    var question = page.questions[questionId],
                        userAnswerId = this.userAnswers[questionId];

                    if (undefined === question.answers[userAnswerId])
                        continue ;

                    var answer = question.answers[userAnswerId];
                    if (answer.correct)
                        ++userScore;
                }
            }

            var userResultTemplate = null;
            for (var i = 0; i < resultTemplates.length; i++) {
                var resultTemplate = resultTemplates[i];

                if (userScore <= resultTemplate.end_point) {
                    userResultTemplate = resultTemplate;
                    break;
                }
            }

            var hasImage = (userResultTemplate && userResultTemplate.image),
                userScorePercent = Math.round( this.questionCount > 0 ? 100 * userScore / this.questionCount : 0 );

            return {
                'maxScore': this.questionCount,

                'userScore': userScore,

                'userScorePercent': userScorePercent,

                'title': userResultTemplate ? userResultTemplate.title : '',

                'content': userResultTemplate ? userResultTemplate.content : '',

                'image_url': (hasImage && userResultTemplate.image.url ? userResultTemplate.image.url : ''),

                'image_description': (hasImage && userResultTemplate.image.description ? userResultTemplate.image.description : '')
            };
        },

        scrollTo: function(el, offset) {
            if (offset === undefined)
                offset = EXTRA_OFFSET;

            var scrollOptions = this.options.scroll.options;
            if (offset != 0) {
                scrollOptions = $.extend(true, {}, scrollOptions);
                scrollOptions.offset += offset;
            }

            $.ariScrollTo(
                el,
                this.options.scroll.duration,
                scrollOptions
            );
        }
    };

    $.fn.ariStreamQuiz = function(options) {
        var quizzes = [];
        this.each(function(i, el) {
            var quizKey = el.id;

            if (quizKey && undefined !== QUIZZES[quizKey]) {
                quizzes.push(QUIZZES[quizKey]);
                return ;
            }

            if (!quizKey)
                quizKey = el.id = generateId();

            var quiz = new Quiz(el, options);
            quizzes.push(quiz);
            QUIZZES[quizKey] = quiz;
        });

        return quizzes.length == 1
            ? quizzes[0]
            : quizzes;
    };

    $.fn.ariStreamQuiz.defaults = {
        'prefix': '',

        'data': {
            'quizId': '',

            'quizType': '',

            'startImmediately': true,

            'collectData': false,

            'collectDataOptional': false,

            'processUserData': false,

            'collectName': false,

            'collectEmail': false,

            'pageCount': 0,

            'questionCount': 0,

            'showResults': 'immediately', //false , 'immediately', 'on_complete'

            'share': {
                'url': '',

                'title': '',

                'description': '',

                'image': ''
            },

            'facebook': {
                'enabled': true,

                'settings': {
                    'appId': ''
                }
            },

            'lockoutAnswers': true
        },

        'ajaxUrl': null,

        'smartScroll': true,

        'lazyLoad': true,

        'warningOnExit': false,

        'scroll': {
            duration: 300,

            options: {
                offset: 0
            }
        },

        'messages': {
            correct: 'Correct',

            wrong: 'Wrong'

        },

        'userDataValidate': function() {
            var isOptional = false,
                collectName = this.options.data.collectName,
                collectEmail = this.options.data.collectEmail,
                isValid = true,
                focusEl = null,
                invalidClass = 'field-invalid',
                errors = [],
                getErrorMessageContainer = function (el) {
                    var containerId = el.attr('id') + '_error',
                        container = $('#' + containerId);

                    if (container.length == 0) {
                        container = $('<div id="' + containerId + '" class="validation-message"></div>').insertAfter(el);
                    }

                    return container;
                };

            if (collectName && !isOptional) {
                var tbxName = $('#' + this.options.prefix + '_userdata_name'),
                    name = $.trim(tbxName.val()),
                    errorMessageContainer = getErrorMessageContainer(tbxName);

                tbxName.removeClass(invalidClass);
                errorMessageContainer.hide();

                if (!name) {
                    tbxName.addClass(invalidClass);
                    errors.push(tbxName.attr('data-validation-message'));
                    isValid = false;

                    errorMessageContainer.html(errors[errors.length - 1]);
                    errorMessageContainer.show();

                    focusEl = tbxName;
                }
            }

            if (collectEmail) {
                var tbxEmail = $('#' + this.options.prefix + '_userdata_email'),
                    email = $.trim(tbxEmail.val()),
                    errorMessageContainer = getErrorMessageContainer(tbxEmail);

                tbxEmail.removeClass(invalidClass);
                errorMessageContainer.hide();

                if (!isOptional && !email) {
                    errors.push(tbxEmail.attr('data-validation-empty-message'));
                    isValid = false;

                    tbxEmail.addClass(invalidClass);
                    errorMessageContainer.html(errors[errors.length - 1]);
                    errorMessageContainer.show();

                    if (!focusEl)
                        focusEl = tbxEmail;
                } else if (email && !(/^(([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5}){1,25})+$/i).test(email)) {
                    errors.push(tbxEmail.attr('data-validation-message'));
                    isValid = false;

                    tbxEmail.addClass(invalidClass);
                    errorMessageContainer.html(errors[errors.length - 1]);
                    errorMessageContainer.show();

                    if (!focusEl)
                        focusEl = tbxEmail;
                }
            }

            if (!isValid) {
                if (focusEl)
                    focusEl.focus();
            }

            return isValid;
        }
    };

    $('.ari-stream-quiz').each(function() {
        var id = $(this).attr('data-id'),
            data = window['ARI_STREAM_QUIZ_' + id] || null;

        if (data)
            $(this).ariStreamQuiz(data);
    });
})(jQuery);