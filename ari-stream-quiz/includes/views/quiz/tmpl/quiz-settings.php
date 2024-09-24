<?php
use Ari_Stream_Quiz\Helpers\Settings;

$mailchimp_apikey = Settings::get_option( 'mailchimp_apikey' );
$mailerlite_apikey = Settings::get_option( 'mailerlite_apikey' );
$question_count_input = '<input type="number" id="tbxQuestionCount" name="entity[random_question_count]" class="input-small center-align" placeholder="X" min="0" size="6" value="' . esc_attr( $entity->random_question_count ) . '" />';
?>
<div class="card-panel">
    <?php do_action( 'asq_ui_quiz_settings_top', $entity ); ?>
    <div class="row">
        <div class="input-field">
            <select class="listbox" id="ddlQuizTheme" name="entity[theme]">
                <option value=""
                <?php
                if ( ! $entity->theme ) :
					?>
                    selected="selected"<?php endif; ?>><?php esc_html_e( '- Default -', 'ari-stream-quiz' ); ?></option>
                <?php
                foreach ( $this->themes as $theme ) :
                    ?>
                    <option value="<?php echo esc_attr( $theme ); ?>"
                        <?php if ( $entity->theme == $theme ) : ?>
                             selected="selected"
                        <?php endif; ?>><?php echo esc_html( $theme ); ?>
                    </option>
					<?php
                endforeach;
                ?>
            </select>
            <label class="label"><?php esc_html_e( 'Theme', 'ari-stream-quiz' ); ?></label>
        </div>
    </div>
    <div class="row">
        <div>
            <label class="label" for="tbxQuizDescription"><?php esc_html_e( 'Description', 'ari-stream-quiz' ); ?></label>
        </div>
        <div>
            <textarea name="entity[quiz_description]" id="tbxQuizDescription" placeholder="<?php esc_attr_e( 'Enter quiz description here', 'ari-stream-quiz' ); ?>"><?php echo esc_attr( $entity->quiz_description ); ?></textarea>
        </div>
    </div>
    <div class="row">
        <input type="checkbox" class="filled-in" id="chkShuffleAnswers" name="entity[shuffle_answers]" value="1"
        <?php
        if ( $entity->shuffle_answers ) :
			?>
            checked="checked"<?php endif; ?> />
        <label for="chkShuffleAnswers" class="label"><?php esc_html_e( 'Shuffle answers', 'ari-stream-quiz' ); ?></label>
    </div>
    <div class="child-controls-inline">
        <input type="checkbox" class="filled-in" id="chkRandomQuestions" name="entity[random_questions]" value="1"
        <?php
        if ( $entity->random_questions ) :
			?>
            checked="checked"<?php endif; ?> />
        <label for="chkRandomQuestions" class="label"><?php esc_html_e( 'Random questions', 'ari-stream-quiz' ); ?></label>
        <label class="label" data-ref-id="chkRandomQuestions">
            <?php
            echo wp_kses(
                sprintf(
                    /* translators: %s: question number input */
                    _x( 'and select %s questions', '%s = question number input', 'ari-stream-quiz' ),
                    $question_count_input
                ),
                array(
					'input' => array(
						'type' => true,
						'id' => true,
						'name' => true,
						'class' => true,
						'placeholder' => true,
						'min' => true,
						'size' => true,
						'value' => true,
					),
                )
            );
			?>
        </label>
    </div>
    <div class="row">
        <input type="checkbox" class="filled-in" id="chkStartImmediately" name="entity[start_immediately]" value="1"
        <?php
        if ( $entity->start_immediately ) :
			?>
            checked="checked"<?php endif; ?> />
        <label class="label" for="chkStartImmediately"><?php esc_html_e( 'Start quiz immediately', 'ari-stream-quiz' ); ?></label>
    </div>
    <div class="row">
        <input type="checkbox" class="filled-in" id="chkShortcode" name="entity[quiz_meta][shortcode]" value="1"
        <?php
        if ( $entity->quiz_meta->shortcode ) :
			?>
            checked="checked"<?php endif; ?> />
        <label class="label" for="chkShortcode"><?php esc_html_e( 'Support shortcodes in questions, quiz description and results', 'ari-stream-quiz' ); ?></label>
        <sup class="teal-text"><?php esc_html_e( 'beta', 'ari-stream-quiz' ); ?></sup>
    </div>
    <div class="row">
        <input type="checkbox" class="filled-in" id="chkShareButtons" name="entity[quiz_meta][show_share_buttons]" value="1"
        <?php
        if ( $entity->quiz_meta->show_share_buttons ) :
			?>
            checked="checked"<?php endif; ?> />
        <label class="label" for="chkShareButtons"><?php esc_html_e( 'Show share buttons', 'ari-stream-quiz' ); ?></label>
    </div>
    <div>
        <div class="row">
            <input type="checkbox" class="filled-in block-switcher" data-ref-id="collectDataContainer" id="chkCollectUserData" name="entity[collect_data]" value="1"
            <?php
            if ( $entity->collect_data ) :
				?>
                checked="checked"<?php endif; ?> />
            <label for="chkCollectUserData" class="label"><?php esc_html_e( 'Collect users\' data', 'ari-stream-quiz' ); ?></label>
        </div>
        <div class="row sub-section" id="collectDataContainer">
            <div class="row">
                <input type="checkbox" class="filled-in" id="chkCollectName" name="entity[collect_name]" value="1"
                <?php
                if ( $entity->collect_name ) :
					?>
                    checked="checked"<?php endif; ?> />
                <label for="chkCollectName" class="label"><?php esc_html_e( 'Ask user name', 'ari-stream-quiz' ); ?></label>
            </div>
            <div class="row">
                <input type="checkbox" class="filled-in" id="chkCollectEmail" name="entity[collect_email]" value="1"
                <?php
                if ( $entity->collect_email ) :
					?>
                    checked="checked"<?php endif; ?> />
                <label for="chkCollectEmail" class="label"><?php esc_html_e( 'Ask e-mail', 'ari-stream-quiz' ); ?></label>
            </div>
            <div class="row">
                <input type="checkbox" class="filled-in" id="chkCollectDataOptional" name="entity[collect_data_optional]" value="1"
                <?php
                if ( $entity->collect_data_optional ) :
					?>
                    checked="checked"<?php endif; ?> />
                <label for="chkCollectDataOptional" class="label"><?php esc_html_e( 'Is optional?', 'ari-stream-quiz' ); ?></label>
            </div>
            <div class="row">
                <input type="checkbox" class="filled-in block-switcher" data-ref-id="mailchimpContainer" id="chkMailchimp" name="entity[quiz_meta][mailchimp][enabled]" value="1"
                <?php
                if ( $entity->quiz_meta->mailchimp->enabled ) :
					?>
                    checked="checked"<?php endif; ?> />
                <label for="chkMailchimp" class="label"><?php esc_html_e( 'MailChimp integration', 'ari-stream-quiz' ); ?></label>
            </div>
            <div class="row sub-section" id="mailchimpContainer">
                <?php
				if ( empty( $mailchimp_apikey ) ) :
					?>
                    <div class="row">
                        <div class="red-text"><?php esc_html_e( 'Enter a MailChimp API key on "Settings" page otherwise integration with MailChimp service will not work.', 'ari-stream-quiz' ); ?></div>
                    </div>
					<?php
                    else :
						?>
                    <div class="row">
                        <div class="col s12">
                            <div>
                                <label class="label" for="ddlMailchimpListId"><?php esc_html_e( 'List', 'ari-stream-quiz' ); ?></label>
                            </div>
                            <div class="clearfix">
                                <select id="ddlMailchimpListId" name="entity[quiz_meta][mailchimp][list_id]" class="browser-default left inline-block" autocomplete="off">
                                    <option value=""><?php esc_html_e( '- None -', 'ari-stream-quiz' ); ?></option>
                                    <?php
                                    if ( $entity->quiz_meta->mailchimp->list_id ) :
                                        ?>
                                        <option value="<?php echo esc_attr( $entity->quiz_meta->mailchimp->list_id ); ?>" selected="selected"><?php echo esc_html( $entity->quiz_meta->mailchimp->list_name ); ?></option>
										<?php
                                    endif;
                                    ?>
                                </select><a href="#" id="mailchimpListRefresh" class="small"><i class="small material-icons">loop</i></a>
                                <input type="hidden" id="hidMailchimpListName" name="entity[quiz_meta][mailchimp][list_name]" value="<?php echo esc_attr( $entity->quiz_meta->mailchimp->list_name ); ?>" />
                            </div>
                        </div>
                    </div>
						<?php
                    endif;
					?>
            </div>

            <div class="row">
                <input type="checkbox" class="filled-in block-switcher" data-ref-id="mailerliteContainer" id="chkMailerLite" name="entity[quiz_meta][mailerlite][enabled]" value="1"
                <?php
                if ( $entity->quiz_meta->mailerlite->enabled ) :
					?>
                    checked="checked"<?php endif; ?> />
                <label for="chkMailerLite" class="label"><?php esc_html_e( 'MailerLite integration', 'ari-stream-quiz' ); ?></label>
            </div>
            <div class="row sub-section" id="mailerliteContainer">
                <?php
				if ( empty( $mailerlite_apikey ) ) :
					?>
                    <div class="row">
                        <div class="red-text"><?php esc_html_e( 'Enter a MailerLite API key on "Settings" page otherwise integration with MailerLite service will not work.', 'ari-stream-quiz' ); ?></div>
                    </div>
					<?php
                    else :
						?>
                    <div class="row">
                        <div class="col s12">
                            <div>
                                <label class="label" for="ddlMailerLiteListId"><?php esc_html_e( 'List', 'ari-stream-quiz' ); ?></label>
                            </div>
                            <div class="clearfix">
                                <select id="ddlMailerLiteListId" name="entity[quiz_meta][mailerlite][list_id]" class="browser-default left inline-block" autocomplete="off">
                                    <option value=""><?php esc_html_e( '- None -', 'ari-stream-quiz' ); ?></option>
                                    <?php
                                    if ( $entity->quiz_meta->mailerlite->list_id ) :
                                        ?>
                                        <option value="<?php echo esc_attr( $entity->quiz_meta->mailerlite->list_id ); ?>" selected="selected"><?php echo esc_html( $entity->quiz_meta->mailerlite->list_name ); ?></option>
										<?php
                                    endif;
                                    ?>
                                </select><a href="#" id="mailerLiteListRefresh" class="small"><i class="small material-icons">loop</i></a>
                                <input type="hidden" id="hidMailerLiteListName" name="entity[quiz_meta][mailerlite][list_name]" value="<?php echo esc_attr( $entity->quiz_meta->mailerlite->list_name ); ?>" />
                            </div>
                        </div>
                    </div>
						<?php
                    endif;
					?>
            </div>
        </div>
    </div>
    <?php do_action( 'asq_ui_quiz_settings_bottom', $entity ); ?>
</div>