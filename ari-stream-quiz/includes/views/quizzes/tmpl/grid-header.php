<?php
$postfix = uniqid( '_hd', false );
$title_column_class = 'manage-column column-primary sortable ' . ( 'quiz_title' == $order_by ? 'sort sort-' . strtolower( $order_dir ) : '' );
$created_on_column_class = 'manage-column sortable ' . ( 'created' == $order_by ? 'sort sort-' . strtolower( $order_dir ) : '' );
$modified_on_column_class = 'manage-column sortable ' . ( 'modified' == $order_by ? 'sort sort-' . strtolower( $order_dir ) : '' );
?>
<tr class="grey lighten-5">
    <th class="manage-column select-column">
        <input type="checkbox" class="filled-in select-all-items chk-quiz" id="<?php echo esc_attr( 'chkAllQuiz' . $postfix ); ?>" autocomplete="off" />
        <label for="chkAllQuiz<?php echo esc_attr( $postfix ); ?>"> </label>
    </th>
    <th class="<?php echo esc_attr( $title_column_class ); ?>" data-sort-column="quiz_title" data-sort-dir="<?php echo esc_attr( 'quiz_title' == $order_by ? $order_dir : '' ); ?>"><div class="column-wrapper"><?php esc_html_e( 'Title', 'ari-stream-quiz' ); ?></div></th>
    <th class="manage-column"><?php esc_html_e( 'Author', 'ari-stream-quiz' ); ?></th>
    <th class="<?php echo esc_attr( $created_on_column_class ); ?>" data-sort-column="created" data-sort-dir="<?php echo esc_attr( 'created' == $order_by ? $order_dir : '' ); ?>"><div class="column-wrapper"><?php esc_html_e( 'Created on', 'ari-stream-quiz' ); ?></div></th>
    <th class="<?php echo esc_attr( $modified_on_column_class ); ?>" data-sort-column="modified" data-sort-dir="<?php echo esc_attr( 'modified' == $order_by ? $order_dir : '' ); ?>"><div class="column-wrapper"><?php esc_html_e( 'Last update', 'ari-stream-quiz' ); ?></div></th>
    <th class="manage-column column-shortcode"><?php esc_html_e( 'Shortcode', 'ari-stream-quiz' ); ?></th>
</tr>