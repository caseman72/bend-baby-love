<?php
/**
 * The template for displaying search forms in portal
 *
 * @package portal
 * @since portal 1.0
 * @license GPL 2.0
 */
?>

<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
	<label for="s" class="assistive-text"><?php _e( 'Search', 'portal' ); ?></label>
	<input type="text" class="field" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" id="s" placeholder="<?php esc_attr_e( 'Search', 'portal' ); ?>" />
	<input type="submit" class="submit" name="submit" id="searchsubmit" value="<?php esc_attr_e( 'Search', 'portal' ); ?>" />
</form>
