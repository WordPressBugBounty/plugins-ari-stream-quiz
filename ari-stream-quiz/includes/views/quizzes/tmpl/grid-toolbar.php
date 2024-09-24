<?php
$visible_page_count = 3;
$filter = $data['filter'];
$page_num = $filter['page_num'];
$page_size = $filter['page_size'];
$count = $data['count'];
$pages_count = $count > 0 ? ( $page_size > 0 ? ceil( $count / $page_size ) : 1 ) : 0;
if ( $visible_page_count > $pages_count ) {
    $visible_page_count = $pages_count;
}

$enabled_first_btn = $page_num > 0;
$enabled_last_btn = $page_num < $pages_count - 1;
$show_paging = ( $pages_count > 1 );

$page_buttons = array();
$buttons_before = min( floor( ( $visible_page_count - 1 ) / 2 ), $page_num );
for ( $i = $buttons_before; $i > 0; $i-- ) {
	$page_buttons[] = $page_num - $i;
}
$page_buttons[] = $page_num;
$buttons_after = min( $visible_page_count - 1 - $buttons_before, $pages_count - $page_num - 1 );
for ( $i = 0; $i < $buttons_after; $i++ ) {
	$page_buttons[] = $page_num + $i + 1;
}

if ( count( $page_buttons ) < $visible_page_count ) {
    $cnt = $visible_page_count - count( $page_buttons );
    for ( $i = 1; $i <= $cnt && $page_buttons[0] > 0; $i++ ) {
        array_unshift( $page_buttons, $page_buttons[0] - 1 );
    }
}
?>

<div class="grid-toolbar row">
    <div class="clearfix col s12
    <?php
    if ( $show_paging ) :
		?>
        m6 l4<?php endif; ?>">
        <select class="bulk-actions-select browser-default left" autocomplete="off">
            <option value="" selected="selected"><?php esc_html_e( '- Bulk actions -', 'ari-stream-quiz' ); ?></option>
            <option value="bulk_copy"><?php esc_html_e( 'Copy', 'ari-stream-quiz' ); ?></option>
            <option value="bulk_delete"><?php esc_html_e( 'Delete', 'ari-stream-quiz' ); ?></option>
        </select>
        &nbsp;<button class="btn btn-cmd blue waves-effect waves-light btn-bulk-apply"><?php esc_html_e( 'Apply', 'ari-stream-quiz' ); ?></button>
    </div>
<?php
if ( $show_paging ) :
	?>
    <div class="col s12 m6 l8 right-align paging">
        <select class="go-to-page right browser-default" autocomplete="off">
            <option value="-1" selected="selected"><?php esc_html_e( 'Go to', 'ari-stream-quiz' ); ?></option>
		<?php
		for ( $i = 0; $i < $pages_count; $i++ ) :
            ?>
            <option value="<?php echo esc_attr( $i ); ?>"
                                      <?php
										if ( $i == $page_num ) :
											?>
                disabled="disabled"<?php endif; ?>><?php echo esc_html( $i + 1 ); ?></option>
            <?php
			endfor;
		?>
        </select>
        <ul class="pagination right">
            <li class="<?php echo $enabled_first_btn ? 'waves-effect' : 'disabled'; ?>"><a href="#"
                                  <?php
									if ( $enabled_first_btn ) :
										?>
                class="grid-page" data-page="0"
										<?php
else :
	?>
                class="disabled" onclick="this.blur();return false;"<?php endif; ?>>«</a></li>
            <li class="<?php echo $enabled_first_btn ? 'waves-effect' : 'disabled'; ?>"><a href="#"
                                  <?php
									if ( $enabled_first_btn ) :
										?>
                class="grid-page" data-page="<?php echo esc_attr( $page_num - 1 ); ?>"
										<?php
else :
	?>
                class="disabled" onclick="this.blur();return false;"<?php endif; ?>>‹</a></li>
            <?php
			//for ( $i = 0; $i < $pages_count; $i++ ):
			foreach ( $page_buttons as $i ) :
				$page_css_class = ( $i == $page_num ) ? 'active blue' : 'waves-effect';
				?>
            <li class="<?php echo esc_attr( $page_css_class ); ?>"><a href="#" class="grid-page" data-page="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i + 1 ); ?></a></li>
				<?php
                endforeach;
            ?>
            <li class="<?php echo $enabled_last_btn ? 'waves-effect' : 'disabled'; ?>"><a href="#"
                                  <?php
									if ( $enabled_last_btn ) :
										?>
                class="grid-page" data-page="<?php echo esc_attr( $page_num + 1 ); ?>"
										<?php
else :
	?>
                class="disabled" onclick="this.blur();return false;"<?php endif; ?>>›</a></li>
            <li class="<?php echo $enabled_last_btn ? 'waves-effect' : 'disabled'; ?>"><a href="#"
                                  <?php
									if ( $enabled_last_btn ) :
										?>
                class="grid-page" data-page="<?php echo esc_attr( $pages_count - 1 ); ?>"
										<?php
else :
	?>
                class="disabled" onclick="this.blur();return false;"<?php endif; ?>>»</a></li>
        </ul>
        <br class="clearfix" />
    </div>
	<?php
    endif;
?>
</div>