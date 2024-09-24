<?php
?>
<?php settings_errors(); ?>
<form method="post" action="options.php" class="settings-page">
    <ul class="tabs" id="quiz_settings_tabs">
        <li class="tab col s3"><a class="teal-text active" href="#general_settings_tab"><?php esc_html_e( 'General', 'ari-stream-quiz' ); ?></a></li>
        <li class="tab col s3"><a class="teal-text" href="#sharing_settings_tab"><?php esc_html_e( 'Sharing', 'ari-stream-quiz' ); ?></a></li>
        <li class="tab col s3"><a class="teal-text" href="#advanced_settings_tab"><?php esc_html_e( 'Advanced', 'ari-stream-quiz' ); ?></a></li>
        <li class="tab col s3"><a class="teal-text" href="#upgrade_tab"><?php esc_html_e( 'Upgrade', 'ari-stream-quiz' ); ?></a></li>
        <div class="indicator teal indicator-fix"></div>
    </ul>
    <div id="general_settings_tab" class="section">
        <div class="card-panel">
            <?php do_settings_sections( ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE ); ?>
        </div>
    </div>
    <div id="sharing_settings_tab" class="section">
        <div class="card-panel">
            <?php do_settings_sections( ARISTREAMQUIZ_SETTINGS_SHARING_PAGE ); ?>
        </div>
    </div>
    <div id="advanced_settings_tab" class="section">
        <div class="card-panel">
            <?php do_settings_sections( ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE ); ?>
        </div>
    </div>
    <div id="upgrade_tab" class="section">
        <div class="card-panel">
            <h6 class="center-align">Need more features? <a href="http://wp-quiz.ari-soft.com/#pricing" target="_blank">Upgrade</a> to PRO version and enjoy with unlimited features:</h6>
            <br /><br />
            <style>UL.pro-features{margin-left:20px;}UL.pro-features LI{list-style-type: disc!important;}</style>
            <ul class="pro-features">
                <li>Personality tests</li>
                <li>Integration with ActiveCampaign, AWeber, Drip, GetResponse, Zapier</li>
                <li>Share results via Pinterest, VKontakte, LinkedIn</li>
                <li>Facebook content locker</li>
                <li>Reports</li>
                <li>Multiple-choice questions</li>
                <li>Export results to CSV</li>
                <li>Create multi-page quizzes</li>
                <li>and many more.</li>
            </ul>
            <br /><br />
            <div class="center-align">
                <a href="http://wp-quiz.ari-soft.com/#pricing" target="_blank" class="btn waves-effect waves-light">UPGRADE TO PRO VERSION</a>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-cmd waves-effect waves-light"><?php esc_html_e( 'Save', 'ari-stream-quiz' ); ?></button>
    <?php settings_fields( ARISTREAMQUIZ_SETTINGS_GROUP ); ?>
</form>