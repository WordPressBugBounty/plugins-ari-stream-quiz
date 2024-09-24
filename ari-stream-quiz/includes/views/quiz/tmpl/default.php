<?php
use Ari_Stream_Quiz\Helpers\Helper;

$entity = $data['entity'];
$action_url = Helper::build_url(
    array(
        'noheader' => '1',
    )
);
$current_path = __DIR__;
?>

<form action="<?php echo esc_url( $action_url ); ?>" method="POST">
<div class="row" id="quizContainer">
    <div class="col s12">
        <h5><?php $entity->is_new() ? esc_html_e( 'Add New Quiz', 'ari-stream-quiz' ) : esc_html_e( 'Edit Quiz', 'ari-stream-quiz' ); ?></h5>
    </div>
    <div class="col s12 l9">
        <input type="text" name="entity[quiz_title]" id="tbxQuizTitle" autocomplete="off" spellcheck="true" placeholder="<?php esc_attr_e( 'Enter quiz title here', 'ari-stream-quiz' ); ?>" value="<?php echo esc_attr( $entity->quiz_title ); ?>" />

        <?php
            require $current_path . '/quiz-trivia.php';
        ?>

        <button class="btn btn-cmd waves-effect waves-light" onclick="AppHelper.trigger(this, 'save'); return false;"><?php esc_html_e( 'Save', 'ari-stream-quiz' ); ?></button>
        <button class="btn btn-cmd waves-effect waves-light" onclick="AppHelper.trigger(this, 'apply'); return false;"><?php esc_html_e( 'Apply', 'ari-stream-quiz' ); ?></button>
        <button class="btn btn-cmd waves-effect waves-light grey lighten-4 black-text" onclick="AppHelper.trigger(this, 'cancel'); return false;"><?php esc_html_e( 'Cancel', 'ari-stream-quiz' ); ?></button>

        <input type="hidden" name="entity[quiz_type]" value="<?php echo esc_attr( $entity->quiz_type ); ?>" />
        <input type="hidden" name="entity[quiz_id]" value="<?php echo esc_attr( $entity->quiz_id ); ?>" />
        <input type="hidden" name="quizActiveTab" id="quizActiveTab" value="<?php echo esc_attr( $this->active_tab ); ?>" />
        <input type="hidden" id="ctrl_action" name="action" value="save" />
    </div>

    <div class="col s12 l3 hide-on-med-and-down">
        <div id="metaBox">
            <?php
                require $current_path . '/quiz-trivia-metabox.php';
            ?>
            <div class="card flex">
                <div class="card-content">
                    <div class="row">
                        <button class="btn btn-cmd waves-effect waves-light full-width" onclick="AppHelper.trigger(this, 'save'); return false;"><?php esc_html_e( 'Save', 'ari-stream-quiz' ); ?></button>
                    </div>
                    <div class="row">
                        <button class="btn btn-cmd waves-effect waves-light full-width" onclick="AppHelper.trigger(this, 'apply'); return false;"><?php esc_html_e( 'Apply', 'ari-stream-quiz' ); ?></button>
                    </div>
                    <div>
                        <button class="btn btn-cmd waves-effect waves-light grey lighten-4 black-text full-width" onclick="AppHelper.trigger(this, 'cancel'); return false;"><?php esc_html_e( 'Cancel', 'ari-stream-quiz' ); ?></button>
                    </div>
                </div>
            </div>
            <div class="card flex">
                <div class="card-content" style="padding-left:0;padding-right:0;">
                    <h6><?php esc_html_e( 'Need more features?', 'ari-stream-quiz' ); ?></h6>
                    <ul class="browser-default" style="padding-left: 20px;">
                        <li><?php esc_html_e( 'Personality tests', 'ari-stream-quiz' ); ?></li>
                        <li><?php esc_html_e( 'ActiveCampaign, AWeber, Drip, GetResponse, Make.com, Zapier integration', 'ari-stream-quiz' ); ?></li>
                        <li><?php esc_html_e( 'Extended social sharing', 'ari-stream-quiz' ); ?></li>
                    </ul>
                    <?php esc_html_e( 'and many more in PRO version.', 'ari-stream-quiz' ); ?>
                </div>
                <div class="card-action">
                    <a class="btn" href="http://wp-quiz.ari-soft.com/#pricing" target="_blank"><?php esc_html_e( 'Get PRO', 'ari-stream-quiz' ); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php wp_nonce_field( $this->edit_quiz_nonce_action ); ?>
</form>