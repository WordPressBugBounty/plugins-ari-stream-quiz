<?php
    $active_tab = $this->active_tab;
?>
<ul class="tabs" id="quiz_settings_tabs">
    <li class="tab col s3"><a class="teal-text
    <?php
    if ( ! $active_tab || 'quiz_result_templates_tab' === $active_tab ) :
		?>
        active<?php endif; ?>" href="#quiz_result_templates_tab" data-container-class="tab-results"><?php esc_html_e( 'Results', 'ari-stream-quiz' ); ?></a></li>
    <li class="tab col s3"><a class="teal-text
    <?php
    if ( 'quiz_questions_tab' === $active_tab ) :
		?>
        active<?php endif; ?>" href="#quiz_questions_tab" data-container-class="tab-questions"><?php esc_html_e( 'Questions', 'ari-stream-quiz' ); ?></a></li>
    <li class="tab col s3"><a class="teal-text
    <?php
    if ( 'quiz_settings_tab' === $active_tab ) :
		?>
        active<?php endif; ?>" href="#quiz_settings_tab" data-container-class="tab-settings"><?php esc_html_e( 'Settings', 'ari-stream-quiz' ); ?></a></li>
    <div class="indicator teal indicator-fix"></div>
</ul>
<div id="quiz_result_templates_tab" class="section"
<?php
if ( $active_tab && 'quiz_result_templates_tab' !== $active_tab ) :
	?>
    style="display:none;"<?php endif; ?>>
    <div id="quiz_result_templates" class="ari-cloner-container" data-cloner-control-key="results" data-cloner-id="results" data-cloner-opt-items="1">
        <div class="center-align">
            <a href="#" class="btn ari-cloner-add-item waves-effect waves-light"><i class="right material-icons">add</i><?php esc_html_e( 'Add result template', 'ari-stream-quiz' ); ?></a>
            <a href="#" id="btnDeleteTemplates" class="btn ari-cloner-removeall-item red waves-effect waves-light"><i class="right material-icons">delete</i><?php esc_html_e( 'Delete all templates', 'ari-stream-quiz' ); ?></a>
        </div>

        <ul class="collapsible-container" data-collapsible="expandable">
            <li class="ari-cloner-template hoverable">
                <div class="collapsible-header sort-handle clearfix">
                    <div class="right actions-panel">
                        <a href="#" class="btn-floating ari-cloner-remove-item red" title=""><i class="material-icons red">delete</i></a>
                    </div>
                    <div class="truncate result-template-title"><?php esc_html_e( 'Untitled', 'ari-stream-quiz' ); ?></div>
                </div>
                <div class="collapsible-body">
                    <div class="card flex">
                        <div class="card-content">
                            <div>
                                <input type="text" placeholder="<?php esc_attr_e( 'Enter title here', 'ari-stream-quiz' ); ?>" data-cloner-type="control" data-cloner-control-key="template_title" />
                            </div>
                            <div>
                                <input type="text" class="disabled input-small center-align black-text" value="0" disabled="disabled" data-cloner-type="control" data-cloner-control-key="start_point" />
                                -
                                <input type="text" class="input-small center-align" placeholder="X" min="0" size="6" data-cloner-type="control" data-cloner-control-key="end_point" />
                                <?php esc_html_e( 'points', 'ari-stream-quiz' ); ?>
                            </div>
                            <div class="ari-wp-image-container">
                                <div class="ari-wp-image-holder" data-lazy-load>
                                </div>
                                <div>
                                    <button class="btn waves-effect waves-light ari-media-library" data-wpmedia-title="<?php esc_attr_e( 'Select image', 'ari-stream-quiz' ); ?>" data-wpmedia-button="<?php esc_attr_e( 'Select image', 'ari-stream-quiz' ); ?>"><i class="right material-icons">image</i><?php esc_html_e( 'Select image', 'ari-stream-quiz' ); ?></button>
                                    <button class="btn waves-effect waves-light ari-media-library-remove red"><?php esc_html_e( 'Remove image', 'ari-stream-quiz' ); ?></button>
                                </div>
                                <input type="hidden" class="ari-wp-image-id" data-ref-column="image" data-cloner-type="control" data-cloner-data-type="image" data-cloner-control-key="image_id" />
                            </div>
                            <div>
                                <div id="tbxResultDescription" data-cloner-type="control" data-cloner-control-key="template_content" data-cloner-data-type="html"></div>
                            </div>
                        </div>
                        <div class="card-action right-align">
                            <a href="#" class="btn-floating ari-cloner-movedown-item light-blue" title=""><i class="material-icons">arrow_drop_down</i></a>
                            <a href="#" class="btn-floating ari-cloner-moveup-item light-blue" title=""><i class="material-icons">arrow_drop_up</i></a>
                            <a href="#" class="btn-floating ari-cloner-remove-item red" title=""><i class="material-icons">delete</i></a>
                        </div>
                    </div>
                    <input type="hidden" data-cloner-type="control" data-cloner-control-key="template_id" />
                </div>
            </li>
        </ul>

        <div class="ari-cloner-empty card-panel" style="display:none;">
            <?php esc_html_e( 'If want to show different content at the end of the quiz based on number of earned points, add result templates with help of "ADD RESULT TEMPLATE +" button.', 'ari-stream-quiz' ); ?>
        </div>
    </div>
</div>
<div id="quiz_questions_tab" class="section"
<?php
if ( 'quiz_questions_tab' !== $active_tab ) :
	?>
    style="display:none;"<?php endif; ?>>
    <div id="quiz_questions" class="ari-cloner-container" data-cloner-control-key="questions" data-cloner-id="questions" data-cloner-opt-items="1">
        <div class="center-align">
            <a href="#" class="btn ari-cloner-add-item waves-effect waves-light"><i class="right material-icons">add</i><?php esc_html_e( 'Add question', 'ari-stream-quiz' ); ?></a>
            <a href="#" id="btnDeleteQuestions" class="btn ari-cloner-removeall-item red waves-effect waves-light"><i class="right material-icons">delete</i><?php esc_html_e( 'Delete all questions', 'ari-stream-quiz' ); ?></a>
        </div>

        <ul class="collapsible-container" data-collapsible="expandable">
            <li class="ari-cloner-template hoverable">
                <div class="collapsible-header sort-handle clearfix">
                    <div class="right actions-panel">
                        <a href="#" class="btn-floating ari-cloner-remove-item red" title=""><i class="material-icons red">delete</i></a>
                    </div>
                    <div class="truncate"><?php esc_html_e( 'Question', 'ari-stream-quiz' ); ?> #<span class="question-index"></span>: <span class="question-text"><?php esc_html_e( 'Untitled', 'ari-stream-quiz' ); ?></span></div>
                </div>
                <div class="collapsible-body">
                    <div class="card flex">
                        <div class="card-content">
                            <div>
                                <input type="text" placeholder="<?php esc_attr_e( 'Enter question', 'ari-stream-quiz' ); ?>" data-cloner-type="control" data-cloner-control-key="question_title" />
                            </div>
                            <div class="ari-wp-image-container">
                                <div class="ari-wp-image-holder" data-lazy-load>
                                </div>
                                <div>
                                    <button class="btn waves-effect waves-light ari-media-library" data-wpmedia-title="<?php esc_attr_e( 'Select image', 'ari-stream-quiz' ); ?>" data-wpmedia-button="<?php esc_attr_e( 'Select image', 'ari-stream-quiz' ); ?>"><i class="right material-icons">image</i><?php esc_html_e( 'Select image', 'ari-stream-quiz' ); ?></button>
                                    <button class="btn waves-effect waves-light ari-media-library-remove red"><?php esc_html_e( 'Remove image', 'ari-stream-quiz' ); ?></button>
                                </div>
                                <input type="hidden" class="ari-wp-image-id" data-ref-column="image" data-cloner-data-type="image" data-cloner-type="control" data-cloner-control-key="image_id" />
                            </div>
                            <div class="ari-cloner-container" data-cloner-id="answers" data-cloner-control-key="answers" data-cloner-opt-items="2">
                                <div class="right-align">
                                    <a href="#" class="btn blue ari-cloner-add-item waves-effect waves-light"><i class="right material-icons">add</i><?php esc_html_e( 'Add answer', 'ari-stream-quiz' ); ?></a>
                                </div>
                                <div class="ari-cloner-template card flex">
                                    <div>
                                        <input type="text" placeholder="<?php esc_attr_e( 'Enter answer', 'ari-stream-quiz' ); ?>" data-cloner-type="control" data-cloner-control-key="answer_title" />
                                    </div>
                                    <div class="ari-wp-image-container">
                                        <div class="ari-wp-image-holder" data-lazy-load>
                                        </div>
                                        <div>
                                            <button class="btn waves-effect waves-light ari-media-library" data-wpmedia-title="<?php esc_attr_e( 'Select image', 'ari-stream-quiz' ); ?>" data-wpmedia-button="<?php esc_attr_e( 'Select image', 'ari-stream-quiz' ); ?>"><i class="right material-icons">image</i><?php esc_html_e( 'Select image', 'ari-stream-quiz' ); ?></button>
                                            <button class="btn waves-effect waves-light ari-media-library-remove red"><?php esc_html_e( 'Remove image', 'ari-stream-quiz' ); ?></button>
                                        </div>
                                        <input type="hidden" class="ari-wp-image-id" data-ref-column="image" data-cloner-data-type="image" data-cloner-type="control" data-cloner-control-key="image_id" />
                                    </div>
                                    <div>
                                        <input type="checkbox" class="filled-in" id="cbCorrectAnswer" data-cloner-type="control" data-cloner-control-key="answer_correct" />
                                        <label for="cbCorrectAnswer" class="label"><?php esc_html_e( 'Is the correct answer', 'ari-stream-quiz' ); ?></label>
                                    </div>
                                    <div class="card-action right-align">
                                        <a href="#" class="btn-floating ari-cloner-movedown-item light-blue" title=""><i class="material-icons">arrow_drop_down</i></a>
                                        <a href="#" class="btn-floating ari-cloner-moveup-item light-blue" title=""><i class="material-icons">arrow_drop_up</i></a>
                                        <a href="#" class="btn-floating ari-cloner-remove-item red" title=""><i class="material-icons">delete</i></a>
                                    </div>
                                    <input type="hidden" data-cloner-type="control" data-cloner-control-key="answer_id" />
                                </div>
                                <div class="ari-cloner-empty card flex red-text" style="display:none;">
                                    <?php esc_html_e( 'Add at least one answer to the question otherwise this question will be ignored and not saved.', 'ari-stream-quiz' ); ?>
                                </div>
                                <div class="right-align">
                                    <a href="#" class="btn blue ari-cloner-add-item waves-effect waves-light"><i class="right material-icons">add</i><?php esc_html_e( 'Add answer', 'ari-stream-quiz' ); ?></a>
                                </div>
                            </div>
                            <?php do_action( 'asq_ui_question_options', $entity ); ?>
                        </div>
                        <div class="card-action right-align">
                            <a href="#" class="btn-floating ari-cloner-movedown-item light-blue" title=""><i class="material-icons">arrow_drop_down</i></a>
                            <a href="#" class="btn-floating ari-cloner-moveup-item light-blue" title=""><i class="material-icons">arrow_drop_up</i></a>
                            <a href="#" class="btn-floating ari-cloner-remove-item red" title=""><i class="material-icons">delete</i></a>
                        </div>
                    </div>
                    <input type="hidden" data-cloner-type="control" data-cloner-control-key="question_id" />
                </div>
            </li>
        </ul>

        <div class="ari-cloner-empty card-panel" style="display:none;">
            <?php esc_html_e( 'Use "ADD QUESTION +" button to populate the quiz with questions. The quiz should contain at least one question.', 'ari-stream-quiz' ); ?>
        </div>
    </div>
</div>
<div id="quiz_settings_tab" class="section"
<?php
if ( 'quiz_settings_tab' !== $active_tab ) :
	?>
    style="display:none;"<?php endif; ?>>
    <?php require __DIR__ . '/quiz-settings.php'; ?>
</div>

<input type="hidden" name="entity[result_templates]" id="hidQuizResultTemplates" value="" />
<input type="hidden" name="entity[questions]" id="hidQuestions" value="" />