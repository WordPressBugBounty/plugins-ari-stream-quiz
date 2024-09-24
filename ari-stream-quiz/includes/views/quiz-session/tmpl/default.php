<?php
use Ari_Stream_Quiz\Helpers\Settings;
use Ari\Wordpress\Helper as WP_Helper;

$quiz = $data['quiz'];
$hide_title = $data['hide_title'];
$column_count = $data['column_count'];
$inline_scripts = $data['inline_scripts'];
$quiz_data = $this->quiz_data;
$current_path = __DIR__;
$prefix = $this->id();
$page_count = count( $quiz_data->pages );
$current_url = get_permalink();
$show_questions_oncomplete = Settings::get_option( 'show_questions_oncomplete' );
$is_trivia = ( ARISTREAMQUIZ_QUIZTYPE_TRIVIA == $quiz->quiz_type );
$twitter_content = $is_trivia ? Settings::get_option( 'share_trivia_twitter_content' ) : Settings::get_option( 'share_personality_twitter_content' );
$facebook_title = $is_trivia ? Settings::get_option( 'share_trivia_facebook_title' ) : Settings::get_option( 'share_personality_facebook_title' );
$facebook_content = $is_trivia ? Settings::get_option( 'share_trivia_facebook_content' ) : Settings::get_option( 'share_personality_facebook_content' );
$email_subject = $is_trivia ? Settings::get_option( 'share_trivia_email_subject' ) : Settings::get_option( 'share_personality_email_subject' );
$email_body = $is_trivia ? Settings::get_option( 'share_trivia_email_body' ) : Settings::get_option( 'share_personality_email_body' );
$support_shortcodes = $quiz->quiz_meta->shortcode;
$lazy_load = Settings::get_option( 'lazy_load' );
$img_tmpl = $current_path . '/image.php';
?>
<?php
if ( $inline_scripts ) :
    $lazy_loaded_scripts = array(
        'scripts' => array(
            ARISTREAMQUIZ_ASSETS_URL . 'scroll_to/jquery.scrollTo.min.js?v=' . ARISTREAMQUIZ_VERSION,
            ARISTREAMQUIZ_ASSETS_URL . 'common/jquery.quiz.js?v=' . ARISTREAMQUIZ_VERSION,
        ),
    );
	?>
<script type="text/javascript">
	<?php
	foreach ( $this->script_vars as $var_name => $var_val ) :
		?>
    window["<?php echo esc_js( $var_name ); ?>"] = <?php echo json_encode( $var_val ); ?>;<?php endforeach; ?></script>
<script type="text/javascript">window.ARI_SCRIPT_LOADER_CONFIG = window.ARI_SCRIPT_LOADER_CONFIG || [];window.ARI_SCRIPT_LOADER_CONFIG.push(<?php echo json_encode( $lazy_loaded_scripts ); ?>);</script>
<script src="<?php /* phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript */ echo esc_url( ARISTREAMQUIZ_ASSETS_URL . 'common/script_loader.js' ); ?>" async></script>
<?php endif; ?>
<div id="<?php echo esc_attr( $prefix . '_container' ); ?>" class="ari-stream-quiz quiz-session-container quiz-<?php echo intval( $quiz->quiz_id, 10 ); ?>
                    <?php
					if ( ! $show_questions_oncomplete ) :
						?>
    hide-questions<?php endif; ?>
    <?php
	if ( $quiz->start_immediately ) :
		?>
    view-quiz-session
		<?php
		if ( $lazy_load ) :
			?>
    quiz-loading<?php endif; ?>
		<?php
else :
	?>
    view-quiz-intro<?php endif; ?>" data-id="<?php echo esc_attr( $prefix ); ?>">
    <div class="quiz-intro">
        <?php
		if ( ! $hide_title ) :
			?>
        <h2 class="quiz-title"><?php echo wp_kses_post( $quiz->quiz_title ); ?></h2>
			<?php
            endif;
        ?>
        <div class="quiz-description">
            <?php echo wp_kses_post( $support_shortcodes && $quiz->quiz_description ? WP_Helper::do_shortcode( $quiz->quiz_description ) : $quiz->quiz_description ); ?>
        </div>

        <?php
		if ( ! $quiz->start_immediately ) :
			?>
        <div class="quiz-actions">
            <button class="button button-salmon button-start-quiz full-width"><?php esc_html_e( 'Start quiz', 'ari-stream-quiz' ); ?></button>
        </div>
			<?php
            endif;
        ?>
    </div>
    <a name="<?php echo esc_attr( $prefix ); ?>_top" id="<?php echo esc_attr( $prefix . '_top' ); ?>"></a>

    <?php
	if ( $lazy_load ) :
		?>
        <div class="quiz-loading-pane" style="display:none;">
            <svg width="72px" height="72px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-squares"><rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect><rect x="15" y="15" width="20" height="20" fill="#cec9c9" class="sq"><animate attributeName="fill" from="#cec9c9" to="#3c302e" repeatCount="indefinite" dur="1s" begin="0.0s" values="#3c302e;#3c302e;#cec9c9;#cec9c9" keyTimes="0;0.1;0.2;1"></animate></rect><rect x="40" y="15" width="20" height="20" fill="#cec9c9" class="sq"><animate attributeName="fill" from="#cec9c9" to="#3c302e" repeatCount="indefinite" dur="1s" begin="0.125s" values="#3c302e;#3c302e;#cec9c9;#cec9c9" keyTimes="0;0.1;0.2;1"></animate></rect><rect x="65" y="15" width="20" height="20" fill="#cec9c9" class="sq"><animate attributeName="fill" from="#cec9c9" to="#3c302e" repeatCount="indefinite" dur="1s" begin="0.25s" values="#3c302e;#3c302e;#cec9c9;#cec9c9" keyTimes="0;0.1;0.2;1"></animate></rect><rect x="15" y="40" width="20" height="20" fill="#cec9c9" class="sq"><animate attributeName="fill" from="#cec9c9" to="#3c302e" repeatCount="indefinite" dur="1s" begin="0.875s" values="#3c302e;#3c302e;#cec9c9;#cec9c9" keyTimes="0;0.1;0.2;1"></animate></rect><rect x="65" y="40" width="20" height="20" fill="#cec9c9" class="sq"><animate attributeName="fill" from="#cec9c9" to="#3c302e" repeatCount="indefinite" dur="1s" begin="0.375" values="#3c302e;#3c302e;#cec9c9;#cec9c9" keyTimes="0;0.1;0.2;1"></animate></rect><rect x="15" y="65" width="20" height="20" fill="#cec9c9" class="sq"><animate attributeName="fill" from="#cec9c9" to="#3c302e" repeatCount="indefinite" dur="1s" begin="0.75s" values="#3c302e;#3c302e;#cec9c9;#cec9c9" keyTimes="0;0.1;0.2;1"></animate></rect><rect x="40" y="65" width="20" height="20" fill="#cec9c9" class="sq"><animate attributeName="fill" from="#cec9c9" to="#3c302e" repeatCount="indefinite" dur="1s" begin="0.625s" values="#3c302e;#3c302e;#cec9c9;#cec9c9" keyTimes="0;0.1;0.2;1"></animate></rect><rect x="65" y="65" width="20" height="20" fill="#cec9c9" class="sq"><animate attributeName="fill" from="#cec9c9" to="#3c302e" repeatCount="indefinite" dur="1s" begin="0.5s" values="#3c302e;#3c302e;#cec9c9;#cec9c9" keyTimes="0;0.1;0.2;1"></animate></rect></svg>
        </div>
		<?php
        endif;
    ?>

    <?php
        $page_num = 0;
        $question_number = 1;
	foreach ( $quiz_data->pages as $quiz_page ) :
		?>
    <div class="quiz-page
		<?php
		if ( 0 == $page_num && $quiz->start_immediately ) :
			?>
        current<?php endif; ?>" id="<?php echo esc_attr( $prefix . '_page_' . $page_num ); ?>" data-page="<?php echo esc_attr( $page_num ); ?>">
		<?php
		foreach ( $quiz_page->questions as $question ) :
			$has_answer_with_image = false;
			foreach ( $question->answers as $answer ) {
				if ( $answer->image_id > 0 ) {
					$has_answer_with_image = true;
					break;
				}
			}
			?>
        <div class="quiz-question
			<?php
			if ( $has_answer_with_image ) :
				?>
            quiz-question-has-image-answer<?php endif; ?>" id="<?php echo esc_attr( $prefix . '_question_' . $question->question_id ); ?>" data-question-id="<?php echo esc_attr( $question->question_id ); ?>">
            <div class="quiz-question-title" data-question-index="<?php echo esc_attr( $question_number ); ?>">
                <?php echo wp_kses_post( $support_shortcodes && strlen( $question->question_title ) > 0 ? WP_Helper::do_shortcode( $question->question_title ) : $question->question_title ); ?>
            </div>
				<?php
                if ( $question->image_id ) :
                    $image = $question->image;
					?>
            <div class="quiz-question-image">
                <div class="quiz-question-image-holder">
                    <?php
                    $this->show_template(
                        $img_tmpl,
                        array(
							'image' => $image,
							'lazy_load' => $lazy_load,
                        )
                    );
					?>
                </div>
            </div>
					<?php
                endif;
				?>
            <div class="quiz-question-answers
            <?php
            if ( $column_count > 1 && $has_answer_with_image ) :
				?>
                answer-col-<?php echo intval( $column_count, 10 ); ?><?php endif; ?> clearfix" id="<?php echo esc_attr( $prefix . '_answers_' . $question->question_id ); ?>">
				<?php
                foreach ( $question->answers as $answer ) :
                    $ctrl_id = $prefix . '_answer_' . $answer->answer_id;
                    $ctrl_name = $prefix . '_answer_' . $question->question_id;
                    $has_image = $answer->image_id > 0;
					?>
                    <div class="quiz-question-answer-holder">
                        <div class="quiz-question-answer" id="<?php echo esc_attr( $prefix . '_answercontainer_' . $answer->answer_id ); ?>">
                            <?php
							if ( $has_answer_with_image ) :
								?>
                            <div class="quiz-question-answer-image">
                                <?php
								if ( $has_image ) :
									$image = $answer->image;
									?>
								<div class="quiz-question-answer-image-wrapper">
                                    <div class="quiz-question-answer-image-holder">
									<?php
                                    $this->show_template(
                                        $img_tmpl,
                                        array(
											'image' => $image,
											'lazy_load' => $lazy_load,
                                        )
                                    );
									?>
                                    </div>
							    </div>
									<?php
                                    endif;
                                ?>
                            </div>
								<?php
                                endif;
                            ?>
                            <div class="quiz-question-answer-controls">
                                <input type="radio" class="ari-checkbox quiz-question-answer-ctrl" name="<?php echo esc_attr( $ctrl_name ); ?>" id="<?php echo esc_attr( $ctrl_id ); ?>" value="<?php echo esc_attr( $answer->answer_id ); ?>" data-question-id="<?php echo esc_attr( $question->question_id ); ?>" />
                                <label class="ari-checkbox-label quiz-question-answer-ctrl-lbl" for="<?php echo esc_attr( $ctrl_id ); ?>"><?php echo wp_kses_post( strlen( $answer->answer_title ) > 0 ? ( $support_shortcodes ? WP_Helper::do_shortcode( $answer->answer_title ) : $answer->answer_title ) : '&nbsp;' ); ?></label>
                            </div>
                        </div>
                    </div>
					<?php
                endforeach;
				?>
            </div>

            <div id="<?php echo esc_attr( $prefix . '_question_status_' . $question->question_id ); ?>" class="quiz-question-status quiz-section" style="display:none;">
                <div class="quiz-question-result"></div>
                <div class="quiz-question-explanation"></div>
            </div>
        </div>
				<?php
                ++$question_number;
            endforeach;
		?>
    </div>
		<?php
		++$page_num;
        endforeach;
    ?>
    <?php
	if ( $quiz->collect_data() ) :
		?>
    <div class="quiz-user-data quiz-section" id="<?php echo esc_attr( $prefix . '_user_data' ); ?>">
        <div class="quiz-label"><?php esc_html_e( 'Complete the form below to see results', 'ari-stream-quiz' ); ?></div>
        <div class="quiz-user-data-form">
		<?php
		if ( $quiz->collect_name ) :
			?>
                <div class="quiz-user-data-name data-row">
                    <label for="<?php echo esc_attr( $prefix . '_userdata_name' ); ?>"><?php esc_html_e( 'Your name', 'ari-stream-quiz' ); ?> :</label>
                    <input type="text" id="<?php echo esc_attr( $prefix . '_userdata_name' ); ?>" data-key="name" autocomplete="off" placeholder="<?php esc_attr_e( 'Enter your name', 'ari-stream-quiz' ); ?>" data-validation-message="<?php esc_attr_e( 'Enter your name', 'ari-stream-quiz' ); ?>" />
                </div>
            <?php
            endif;
		?>
		<?php
		if ( $quiz->collect_email ) :
			?>
                <div class="quiz-user-data-email data-row">
                    <label for="<?php echo esc_attr( $prefix . '_userdata_email' ); ?>"><?php esc_html_e( 'Your e-mail', 'ari-stream-quiz' ); ?> :</label>
                    <input type="text" id="<?php echo esc_attr( $prefix . '_userdata_email' ); ?>" data-key="email" autocomplete="off" placeholder="<?php esc_attr_e( 'Enter your e-mail', 'ari-stream-quiz' ); ?>" data-validation-empty-message="<?php esc_attr_e( 'Enter your email', 'ari-stream-quiz' ); ?>" data-validation-message="<?php esc_attr_e( 'Enter correct email', 'ari-stream-quiz' ); ?>" />
                </div>
            <?php
            endif;
		?>
        </div>
        <div class="quiz-actions">
            <button class="button button-blue full-width button-collect-data"><?php esc_html_e( 'Show results', 'ari-stream-quiz' ); ?></button>
            <?php
            if ( $quiz->collect_data_optional ) :
                ?>
                <button class="button button-alge full-width button-skip-collect-data"><?php esc_html_e( 'Skip and Show results', 'ari-stream-quiz' ); ?></button>
				<?php
            endif;
            ?>
        </div>
    </div>
		<?php
        endif;
    ?>
    <div class="quiz-result quiz-section" id="<?php echo esc_attr( $prefix . '_result' ); ?>">
        <div class="quiz-result-template" id="<?php echo esc_attr( $prefix . '_result_template' ); ?>">
            <div class="quiz-title"><?php echo wp_kses_post( $quiz->quiz_title ); ?></div>
            <div class="quiz-score"><?php echo wp_kses_post( Settings::get_option( 'share_trivia_title' ) ); ?></div>
            <div class="result-title">{{title}}</div>
            <div class="result-image {{image_class}}" data-image-credit="{{image_credit}}">{{image}}</div>
            <div class="result-content">{{content}}</div>
        </div>
        <div class="quiz-result-wrapper"></div>
        <?php
		if ( (bool) $quiz->quiz_meta->show_share_buttons && count( $this->share_buttons ) > 0 ) :
			?>
        <div class="quiz-result-share-buttons">
            <div class="share-title"><?php esc_html_e( 'Share your result via', 'ari-stream-quiz' ); ?></div>
            <div class="buttons-container">
			<?php
			if ( in_array( 'facebook', $this->share_buttons ) ) :
                ?>
                    <a href="https://www.facebook.com/sharer.php?u=<?php echo rawurlencode( $current_url ); ?>" class="button button-share button-facebook" target="_blank" data-share-title="<?php echo esc_attr( $facebook_title ); ?>" data-share-description="<?php echo esc_attr( $facebook_content ); ?>">
                        <i>
                            <svg version="1.1" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
                                <g>
                                    <path d="M18.768,7.465H14.5V5.56c0-0.896,0.594-1.105,1.012-1.105s2.988,0,2.988,0V0.513L14.171,0.5C10.244,0.5,9.5,3.438,9.5,5.32 v2.145h-3v4h3c0,5.212,0,12,0,12h5c0,0,0-6.85,0-12h3.851L18.768,7.465z"/>
                                </g>
                            </svg>
                        </i>
                        <span><?php esc_html_e( 'Facebook', 'ari-stream-quiz' ); ?></span>
                    </a>
                <?php
				endif;
			?>
                <?php
				if ( in_array( 'twitter', $this->share_buttons ) ) :
					?>
                    <a href="#" class="button button-share button-twitter" data-share-url="https://twitter.com/intent/tweet?original_referer={{url}}&url={{url}}&text={{item_content}}" target="_blank" data-share-description="<?php echo esc_attr( $twitter_content ); ?>">
                        <i>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g id="surface1">
                                    <path style=" stroke:none;fill-rule:evenodd;fill:rgb(98.039216%,98.039216%,98.039216%);fill-opacity:1;" d="M 4.925781 4.898438 C 4.972656 4.96875 6.09375 6.597656 7.414062 8.519531 C 8.730469 10.441406 9.953125 12.222656 10.125 12.472656 C 10.296875 12.722656 10.4375 12.933594 10.4375 12.9375 C 10.4375 12.945312 10.375 13.023438 10.296875 13.113281 C 10.21875 13.203125 9.996094 13.460938 9.804688 13.683594 C 9.613281 13.90625 9.292969 14.28125 9.089844 14.515625 C 8.886719 14.753906 8.527344 15.167969 8.292969 15.4375 C 8.0625 15.710938 7.640625 16.199219 7.363281 16.523438 C 6.496094 17.527344 6.371094 17.675781 5.714844 18.441406 C 5.363281 18.851562 5.027344 19.242188 4.964844 19.3125 C 4.90625 19.378906 4.855469 19.445312 4.855469 19.453125 C 4.855469 19.464844 5.074219 19.46875 5.476562 19.46875 L 6.097656 19.46875 L 6.78125 18.671875 C 7.160156 18.234375 7.53125 17.800781 7.609375 17.710938 C 7.773438 17.519531 9.050781 16.035156 9.15625 15.910156 C 9.199219 15.859375 9.257812 15.792969 9.289062 15.753906 C 9.324219 15.71875 9.574219 15.425781 9.847656 15.105469 C 10.125 14.785156 10.355469 14.515625 10.363281 14.507812 C 10.375 14.496094 10.519531 14.328125 10.6875 14.132812 C 10.855469 13.933594 11 13.773438 11.007812 13.773438 C 11.011719 13.773438 11.878906 15.027344 12.933594 16.558594 C 13.984375 18.09375 14.863281 19.375 14.886719 19.40625 L 14.929688 19.46875 L 17.0625 19.46875 C 18.816406 19.46875 19.195312 19.464844 19.1875 19.449219 C 19.183594 19.429688 18.160156 17.9375 15.535156 14.121094 C 13.648438 11.371094 13.398438 11.003906 13.40625 10.984375 C 13.410156 10.964844 13.667969 10.664062 15.359375 8.695312 C 15.648438 8.359375 16.050781 7.894531 16.246094 7.664062 C 16.445312 7.429688 16.648438 7.199219 16.695312 7.144531 C 16.738281 7.089844 16.984375 6.808594 17.238281 6.515625 C 17.488281 6.21875 17.917969 5.722656 18.191406 5.40625 C 18.460938 5.089844 18.695312 4.816406 18.707031 4.800781 C 18.726562 4.777344 18.691406 4.773438 18.097656 4.773438 L 17.464844 4.773438 L 17.183594 5.101562 C 16.808594 5.539062 16.132812 6.324219 15.941406 6.546875 C 15.855469 6.644531 15.75 6.769531 15.707031 6.820312 C 15.660156 6.875 15.574219 6.976562 15.511719 7.046875 C 15.449219 7.117188 15.132812 7.488281 14.808594 7.863281 C 14.484375 8.242188 14.214844 8.554688 14.207031 8.5625 C 14.203125 8.566406 14.132812 8.644531 14.054688 8.738281 C 13.914062 8.90625 13.773438 9.070312 13.125 9.820312 C 12.839844 10.152344 12.824219 10.164062 12.800781 10.136719 C 12.789062 10.117188 11.953125 8.90625 10.949219 7.4375 L 9.121094 4.773438 L 4.835938 4.773438 L 4.925781 4.898438 M 6.597656 5.773438 C 6.613281 5.796875 7.011719 6.371094 7.484375 7.046875 C 8.382812 8.332031 11.90625 13.375 14.171875 16.621094 C 14.898438 17.65625 15.5 18.515625 15.507812 18.527344 C 15.523438 18.542969 15.734375 18.546875 16.496094 18.542969 L 17.460938 18.539062 L 14.933594 14.921875 C 13.542969 12.933594 11.53125 10.050781 10.460938 8.519531 L 8.515625 5.738281 L 7.542969 5.734375 L 6.566406 5.730469 L 6.597656 5.773438 "/>
                                    <path style=" stroke:none;fill-rule:evenodd;fill:rgb(1.568627%,1.568627%,1.568627%);fill-opacity:1;" d="M 0 12 L 0 24 L 24.007812 23.992188 L 24.015625 0 L 0 0 L 0 12 M 0.0078125 12.007812 C 0.0078125 18.609375 0.0117188 21.308594 0.0117188 18.007812 C 0.015625 14.707031 0.015625 9.308594 0.0117188 6.007812 C 0.0117188 2.707031 0.0078125 5.40625 0.0078125 12.007812 M 4.925781 4.898438 C 4.972656 4.96875 6.09375 6.597656 7.414062 8.519531 C 8.730469 10.441406 9.953125 12.222656 10.125 12.472656 C 10.296875 12.722656 10.4375 12.933594 10.4375 12.9375 C 10.4375 12.945312 10.375 13.023438 10.296875 13.113281 C 10.21875 13.203125 9.996094 13.460938 9.804688 13.683594 C 9.613281 13.90625 9.292969 14.28125 9.089844 14.515625 C 8.886719 14.753906 8.527344 15.167969 8.292969 15.4375 C 8.0625 15.710938 7.640625 16.199219 7.363281 16.523438 C 6.496094 17.527344 6.371094 17.675781 5.714844 18.441406 C 5.363281 18.851562 5.027344 19.242188 4.964844 19.3125 C 4.90625 19.378906 4.855469 19.445312 4.855469 19.453125 C 4.855469 19.464844 5.074219 19.46875 5.476562 19.46875 L 6.097656 19.46875 L 6.78125 18.671875 C 7.160156 18.234375 7.53125 17.800781 7.609375 17.710938 C 7.773438 17.519531 9.050781 16.035156 9.15625 15.910156 C 9.199219 15.859375 9.257812 15.792969 9.289062 15.753906 C 9.324219 15.71875 9.574219 15.425781 9.847656 15.105469 C 10.125 14.785156 10.355469 14.515625 10.363281 14.507812 C 10.375 14.496094 10.519531 14.328125 10.6875 14.132812 C 10.855469 13.933594 11 13.773438 11.007812 13.773438 C 11.011719 13.773438 11.878906 15.027344 12.933594 16.558594 C 13.984375 18.09375 14.863281 19.375 14.886719 19.40625 L 14.929688 19.46875 L 17.0625 19.46875 C 18.816406 19.46875 19.195312 19.464844 19.1875 19.449219 C 19.183594 19.429688 18.160156 17.9375 15.535156 14.121094 C 13.648438 11.371094 13.398438 11.003906 13.40625 10.984375 C 13.410156 10.964844 13.667969 10.664062 15.359375 8.695312 C 15.648438 8.359375 16.050781 7.894531 16.246094 7.664062 C 16.445312 7.429688 16.648438 7.199219 16.695312 7.144531 C 16.738281 7.089844 16.984375 6.808594 17.238281 6.515625 C 17.488281 6.21875 17.917969 5.722656 18.191406 5.40625 C 18.460938 5.089844 18.695312 4.816406 18.707031 4.800781 C 18.726562 4.777344 18.691406 4.773438 18.097656 4.773438 L 17.464844 4.773438 L 17.183594 5.101562 C 16.808594 5.539062 16.132812 6.324219 15.941406 6.546875 C 15.855469 6.644531 15.75 6.769531 15.707031 6.820312 C 15.660156 6.875 15.574219 6.976562 15.511719 7.046875 C 15.449219 7.117188 15.132812 7.488281 14.808594 7.863281 C 14.484375 8.242188 14.214844 8.554688 14.207031 8.5625 C 14.203125 8.566406 14.132812 8.644531 14.054688 8.738281 C 13.914062 8.90625 13.773438 9.070312 13.125 9.820312 C 12.839844 10.152344 12.824219 10.164062 12.800781 10.136719 C 12.789062 10.117188 11.953125 8.90625 10.949219 7.4375 L 9.121094 4.773438 L 4.835938 4.773438 L 4.925781 4.898438 M 6.597656 5.773438 C 6.613281 5.796875 7.011719 6.371094 7.484375 7.046875 C 8.382812 8.332031 11.90625 13.375 14.171875 16.621094 C 14.898438 17.65625 15.5 18.515625 15.507812 18.527344 C 15.523438 18.542969 15.734375 18.546875 16.496094 18.542969 L 17.460938 18.539062 L 14.933594 14.921875 C 13.542969 12.933594 11.53125 10.050781 10.460938 8.519531 L 8.515625 5.738281 L 7.542969 5.734375 L 6.566406 5.730469 L 6.597656 5.773438 "/>
                                </g>
                            </svg>
                        </i>
                        <span><?php esc_html_e( 'X (Twitter)', 'ari-stream-quiz' ); ?></span>
                    </a>
					<?php
                    endif;
                ?>
                <?php
				if ( in_array( 'email', $this->share_buttons ) ) :
					?>
                    <a href="#" class="button button-share button-email" data-share-url="mailto:?body={{item_content}}&subject={{item_title}}" data-share-disable-modal="1" data-share-title="<?php echo esc_attr( $email_subject ); ?>" data-share-description="<?php echo esc_attr( $email_body ); ?>">
                        <i>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 3v18h24v-18h-24zm21.518 2l-9.518 7.713-9.518-7.713h19.036zm-19.518 14v-11.817l10 8.104 10-8.104v11.817h-20z"/></svg>
                        </i>
                        <span><?php esc_html_e( 'Email', 'ari-stream-quiz' ); ?></span>
                    </a>
					<?php
                    endif;
                ?>
            </div>
        </div>
			<?php
            endif;
        ?>
    </div>
</div>