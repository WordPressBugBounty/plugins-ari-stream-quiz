<?php
use Ari_Stream_Quiz\Helpers\Helper;

$can_edit_other_quizzes = Helper::can_edit_other_quizzes();
$current_user_id = get_current_user_id();
$list = $data['list'];
$filter = $data['filter'];
$order_by = $filter['order_by'];
$order_dir = $filter['order_dir'];

$remove_url_params = array( 'filter', 'preview' );
$add_trivia_quiz_url = Helper::build_url(
    array(
        'page' => 'ari-stream-quiz-quiz',
        'action' => 'add',
        'type' => ARISTREAMQUIZ_QUIZTYPE_TRIVIA,
    ),
    $remove_url_params
);
$edit_url = Helper::build_url(
    array(
        'page' => 'ari-stream-quiz-quiz',
        'action' => 'edit',
        'id' => '__quizId__',
    ),
    $remove_url_params
);
$preview_quiz_url = Helper::build_url(
    array(
        'page' => 'ari-stream-quiz-quizzes',
        'action' => 'preview',
        'post_id' => '__postId__',
    ),
    $remove_url_params
);
$action_url = Helper::build_url(
    array(
        'noheader' => '1',
    ),
    $remove_url_params
);
$action_url = wp_nonce_url( $action_url, 'asq-quiz' );

/* translators: %1$s: new post link, %2$s: new page link */
$create_post_lbl = _x(
    'Create <a href="%1$s" target="_blank">post</a> / <a href="%2$s" target="_blank">page</a>',
    '%1$s = new post link, %2$s = new page link',
    'ari-stream-quiz'
);
$date_time_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
$current_path = __DIR__;

/* translators: %s: human-readable time difference */
$time_ago_format = _x( '%s ago', '%s = human-readable time difference', 'ari-stream-quiz' );
$col_names = array(
    'title' => __( 'Title', 'ari-stream-quiz' ),

    'type' => __( 'Type', 'ari-stream-quiz' ),

    'author' => __( 'Author', 'ari-stream-quiz' ),

    'created' => __( 'Created on', 'ari-stream-quiz' ),

    'modified' => __( 'Last update', 'ari-stream-quiz' ),

    'shortcode' => __( 'Shortcode', 'ari-stream-quiz' ),
);
$col_names_attr = array();
foreach ( $col_names as $col_name_key => $col_name ) {
    $col_names_attr[ $col_name_key ] = esc_attr( $col_name );
}
?>
<div>
    <a href="<?php echo esc_url( $add_trivia_quiz_url ); ?>" class="btn waves-effect waves-light"><i class="right material-icons hide-on-small-only">add</i><?php esc_html_e( 'Add a trivia quiz', 'ari-stream-quiz' ); ?></a>
</div>
<form action="<?php echo esc_url( $action_url ); ?>" method="POST">
    <div class="card-panel">
        <div class="row">
            <div class="col s12">
                <input type="text" autocomplete="off" id="tbxSearchText" name="quiz_search[search]" placeholder="<?php esc_attr_e( 'Search...', 'ari-stream-quiz' ); ?>" value="<?php echo esc_attr( $filter['search'] ); ?>" />
            </div>
        </div>
        <div class="row">
            <div class="col m4 left-align hide-on-small-only">
                <div class="grid-search-message"><?php echo esc_html( printf( /* translators: %d: items count */ _x( '%d items found.', '%d = items count', 'ari-stream-quiz' ), $data['count'] ) ); ?></div>
            </div>
            <div class="col s12 m8 right-align">
                <a href="#" id="btnQuizSearch" class="btn btn-cmd blue waves-effect waves-light"><i class="right material-icons hide-on-small-only">search</i><?php esc_html_e( 'Search', 'ari-stream-quiz' ); ?></a>
                <a href="#" id="btnQuizSearchReset" class="btn btn-cmd red waves-effect waves-light"><i class="right material-icons hide-on-small-only">clear</i><?php esc_html_e( 'Reset', 'ari-stream-quiz' ); ?></a>
            </div>
        </div>
    </div>

    <div class="hide-on-small-only">
    <?php
        $this->show_template( $current_path . '/grid-toolbar.php', $data );
    ?>
    </div>

    <table id="gridQuizzes" class="striped ari-grid z-depth-1">
        <thead>
            <?php
                require $current_path . '/grid-header.php';
            ?>
        </thead>
        <tbody>
            <?php
			if ( is_array( $list ) && count( $list ) > 0 ) :
				foreach ( $list as $item ) :
					$can_edit = $can_edit_other_quizzes || $current_user_id == $item->author_id;
					$preview_item_url = $item->post_id > 0 ? str_replace( '__postId__', $item->post_id, $preview_quiz_url ) : '';
					$item_edit_url = str_replace( '__quizId__', $item->quiz_id, $edit_url );
					$tbx_shortcode_id = 'asq_shortcode_' . $item->quiz_id;
					$shortcode = '[streamquiz id="' . $item->quiz_id . '"]';
					$checkbox_id = 'chkQuiz_' . $item->quiz_id;
					?>
            <tr>
                <td class="select-column">
                    <?php
					if ( $can_edit ) :
						?>
                    <input type="checkbox" autocomplete="off" class="filled-in chk-quiz" name="quiz_id[]" id="<?php echo esc_attr( $checkbox_id ); ?>" value="<?php echo esc_attr( $item->quiz_id ); ?>" />
                    <label for="<?php echo esc_attr( $checkbox_id ); ?>"> </label>
						<?php
                        endif;
                    ?>
                </td>
                <td class="quiz-title column-primary">
                    <?php
					if ( $can_edit ) :
						?>
                    <a class="quiz-title" href="<?php echo esc_url( $item_edit_url ); ?>"><?php echo esc_html( $item->quiz_title_filtered ? $item->quiz_title_filtered : $item->quiz_title ); ?></a>
						<?php
                        else :
							?>
                    <span class="quiz-title"><?php echo esc_html( $item->quiz_title_filtered ? $item->quiz_title_filtered : $item->quiz_title ); ?></span>
							<?php
                        endif;
						?>
                    <div class="grid-row-actions">
                        <?php
						if ( $can_edit ) :
							?>
                        <a href="<?php echo esc_url( $item_edit_url ); ?>"><?php esc_html_e( 'Edit', 'ari-stream-quiz' ); ?></a>
                        |
                        <a href="#" class="btn-quiz-copy" data-quiz-id="<?php echo esc_attr( $item->quiz_id ); ?>"><?php esc_html_e( 'Copy', 'ari-stream-quiz' ); ?></a>
                        |
                        <a href="#" class="red-text btn-quiz-delete" data-quiz-id="<?php echo esc_attr( $item->quiz_id ); ?>"><?php esc_html_e( 'Delete', 'ari-stream-quiz' ); ?></a>
							<?php
                            endif;
                        ?>
                        <?php
						if ( $preview_item_url ) :
							?>
							<?php
                            if ( $can_edit ) :
								?>
                                | <?php endif; ?>
                        <a href="<?php echo esc_url( $preview_item_url ); ?>" target="_blank"><?php esc_html_e( 'View', 'ari-stream-quiz' ); ?></a>
							<?php
                            endif;
                        ?>
                    </div>
                    <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
                </td>
                <td class="quiz-author" data-colname="<?php echo esc_attr( $col_names_attr['author'] ); ?>">
                    <?php echo esc_html( $item->author ); ?>
                </td>
                <td class="quiz-created" data-colname="<?php echo esc_attr( $col_names_attr['created'] ); ?>">
                    <?php
					echo esc_html( date_i18n( $date_time_format, get_date_from_gmt( $item->created, 'U' ) ) );
                    ?>
                </td>
                <td class="quiz-modified" data-colname="<?php echo esc_attr( $col_names_attr['modified'] ); ?>">
                    <?php
					echo esc_attr( ARISTREAMQUIZ_DB_EMPTYDATE != $item->modified ? sprintf( $time_ago_format, human_time_diff( mysql2date( 'G', $item->modified ), time() ) ) : '—' );
                    ?>
                </td>
                <td class="quiz-shortcode" data-colname="<?php echo esc_attr( $col_names_attr['shortcode'] ); ?>">
                    <input class="black-text" type="text" id="<?php echo esc_attr( $tbx_shortcode_id ); ?>" size="30" readonly="readonly" value="<?php echo esc_attr( $shortcode ); ?>" />
                    <a href="#" class="asq-shortcode-btn-copy" onclick="return false;" data-clipboard-target="<?php echo esc_attr( '#' . $tbx_shortcode_id ); ?>"><?php esc_html_e( 'Copy to clipboard', 'ari-stream-quiz' ); ?></a>
                    <hr />
                    <?php
                    echo wp_kses(
                        sprintf(
                            $create_post_lbl,
                            esc_url( admin_url( 'post-new.php?stream_quiz[id]=' . $item->quiz_id . '&stream_quiz[title]=' . rawurlencode( $item->quiz_title ) ) ),
                            esc_url( admin_url( 'post-new.php?post_type=page&stream_quiz[id]=' . $item->quiz_id . '&stream_quiz[title]=' . rawurlencode( $item->quiz_title ) ) )
                        ),
                        array(
                            'a' => array(
                                'href' => true,
                                'target' => true,
                            ),
                        )
                    );
					?>
                </td>
            </tr>
					<?php
                    endforeach;
                else :
					?>
            <tr class="no-items">
                <td class="colspanchange" colspan="6">
                    <?php _e( 'No quizzes found', 'ari-stream-quiz' ); ?>
                </td>
            </tr>
					<?php
                endif;
				?>
        </tbody>
        <tfoot>
            <?php
                require $current_path . '/grid-header.php';
            ?>
        </tfoot>
    </table>

    <?php
        require $current_path . '/grid-toolbar.php';
    ?>

    <input type="hidden" id="ctrl_action" name="action" value="display" />
    <input type="hidden" id="hidQuizzesSortBy" name="quiz_sort[column]" value="" />
    <input type="hidden" id="hidQuizzesSortDir" name="quiz_sort[dir]" value="" />
    <input type="hidden" id="hidQuizzesPageNum" name="quiz_page" value="-1" />
    <input type="hidden" id="hidQuizId" name="action_quiz_id" value="" />
    <input type="hidden" id="hidPostId" name="action_post_id" value="" />
    <input type="hidden" name="filter" value="<?php echo esc_attr( $data['filter_encoded'] ); ?>" />
</form>