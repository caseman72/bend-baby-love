<?php
/**
 * Custom template tags for this theme.
 *
 * @package portal
 * @since portal 1.0
 * @license GPL 2.0
 */

if ( ! function_exists( 'portal_content_nav' ) ) :
/**
 * Display navigation to next/previous pages when applicable
 *
 * @since portal 1.0
 */
function portal_content_nav( $nav_id ) {
	global $wp_query, $post;

	// Don't print empty markup on single pages if there's nowhere to navigate.
	if ( is_single() ) {
		$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
		$next = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous )
			return;
	}

	// Don't print empty markup in archives if there's only one page.
	if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) )
		return;

	$nav_class = 'site-navigation paging-navigation';
	if ( is_single() )
		$nav_class = 'site-navigation post-navigation';

	?>
	<nav role="navigation" id="<?php echo esc_attr( $nav_id ); ?>" class="<?php echo $nav_class; ?>">
		<h1 class="assistive-text"><?php _e( 'Post navigation', 'portal' ); ?></h1>

	<?php if ( is_single() ) : // navigation links for single posts ?>

		<?php previous_post_link( '<div class="nav-previous">%link</div>', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'portal' ) . '</span> %title' ); ?>
		<?php next_post_link( '<div class="nav-next">%link</div>', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'portal' ) . '</span>' ); ?>

	<?php elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) : // navigation links for home, archive, and search pages ?>

		<?php if ( get_next_posts_link() ) : ?>
		<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'portal' ) ); ?></div>
		<?php endif; ?>

		<?php if ( get_previous_posts_link() ) : ?>
		<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'portal' ) ); ?></div>
		<?php endif; ?>

	<?php endif; ?>

	</nav><!-- #<?php echo esc_html( $nav_id ); ?> -->
	<?php
}
endif; // portal_content_nav

if ( ! function_exists( 'portal_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since portal 1.0
 */
function portal_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
			?>
			<li class="post pingback">
				<p><?php _e( 'Pingback:', 'portal' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'portal' ), ' ' ); ?></p>
			<?php
			break;
		default :
			?>
			<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
				<article id="comment-<?php comment_ID(); ?>" class="comment">
					<footer>
						<?php echo get_avatar( $comment, 50 ); ?>
						<div class="comment-author">
							<cite class="fn"><?php comment_author_link() ?></cite>
						</div><!-- .comment-author -->


						<div class="comment-meta commentmetadata">
							<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><time datetime="<?php comment_time( 'c' ); ?>"><?php
								/* translators: 1: date, 2: time */
								printf( __( '%1$s at %2$s', 'portal' ), get_comment_date(), get_comment_time() );
								?></time></a>

							<span class="support">
								<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
								<?php edit_comment_link( __( 'Edit', 'portal' ), ' ' ); ?>
							</span>
						</div><!-- .comment-meta .commentmetadata -->

						<?php if ( $comment->comment_approved == '0' ) : ?>
							<em class="awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'portal' ); ?></em>
						<?php endif; ?>
					</footer>
		
					<div class="comment-content entry-content"><?php comment_text(); ?></div>
				</article><!-- #comment-## -->
		
			<?php
			break;
	endswitch;
}
endif; // ends check for portal_comment()

if ( ! function_exists( 'portal_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 *
 * @since portal 1.0
 */
function portal_posted_on() {
	printf( __( 'Posted on <a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a><span class="byline"> by <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'portal' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'portal' ), get_the_author() ) ),
		get_the_author()
	);
}
endif;

if(!function_exists('portal_display_logo')):
/**
 * Display the logo 
 */
function portal_display_logo(){
	$logo = siteorigin_setting('general_logo');

	if(empty($logo)) {
		// Just display the site title
		bloginfo( 'name' );
		return;
	}
	
	// load the logo image
	$image = wp_get_attachment_image_src($logo, 'full');
	$height = $image[2];
	$width = $image[1];

	// echo $image;
	?><img src="<?php echo $image[0] ?>" width="<?php echo round($width) ?>" height="<?php echo round($height) ?>" /><?php
}
endif;

/**
 * Returns true if a blog has more than 1 category
 *
 * @since portal 1.0
 */
function portal_categorized_blog() {
	if ( false === ( $count = get_transient( 'portal_categorized_blog_cache_count' ) ) ) {
		// Count the number of non-empty categories
		$count = count( get_categories( array(
			'hide_empty' => 1,
		) ) );
		
		// Count the number of categories that are attached to the posts
		set_transient( 'portal_categorized_blog_cache_count', $count );
	}
	
	// Return true if this blog has categories, or else false.
	return ($count >= 1);
}

/**
 * Flush out the transients used in portal_categorized_blog
 *
 * @since portal 1.0
 */
function portal_category_transient_flusher() {
	delete_transient( 'portal_categorized_blog_cache_count' );
}
add_action( 'edit_category', 'portal_category_transient_flusher' );
add_action( 'save_post', 'portal_category_transient_flusher' );

/**
 * Return the archive title depending on which page is being displayed.
 * 
 * @since portal 1.0
 */
function portal_get_archive_title(){
	$title = '';
	if ( is_category() ) {
		$title = sprintf( __( 'Category: %s', 'portal' ), '<span>' . single_cat_title( '', false ) . '</span>' );

	}
	elseif ( is_tag() ) {
		$title = sprintf( __( 'Tag: %s', 'portal' ), '<span>' . single_tag_title( '', false ) . '</span>' );

	}
	elseif ( is_author() ) {
		the_post();
		$title = sprintf( __( 'Author: %s', 'portal' ), '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( "ID" ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' );
		rewind_posts();

	}
	elseif ( is_day() ) {
		$title = sprintf( __( 'Daily Archives: %s', 'portal' ), '<span>' . get_the_date() . '</span>' );

	}
	elseif ( is_month() ) {
		$title = sprintf( __( 'Monthly Archives: %s', 'portal' ), '<span>' . get_the_date( 'F Y' ) . '</span>' );

	}
	elseif ( is_year() ) {
		$title = sprintf( __( 'Yearly Archives: %s', 'portal' ), '<span>' . get_the_date( 'Y' ) . '</span>' );

	}
	else {
		$title = __( 'Posts', 'portal' );
	}
	
	return apply_filters('portal_archive_title', $title);
}

/**
 * Get the post meta.
 * 
 * @since portal 1.0
 */
function portal_get_post_meta(){
	/* translators: used between list items, there is a space after the comma */
	$category_list = get_the_category_list( __( ', ', 'portal' ) );

	/* translators: used between list items, there is a space after the comma */
	$tag_list = get_the_tag_list( '', __( ', ', 'portal' ) );
	$meta_text = '';

	if ( ! portal_categorized_blog() ) {
		// This blog only has 1 category so we just need to worry about tags in the meta text
		if ( '' != $tag_list ) {
			$meta_text = __( 'Tagged %2$s.', 'portal' );
		}
	}
	else {
		// But this blog has loads of categories so we should probably display them here
		if ( '' != $tag_list ) {
			$meta_text = __( 'Posted in %1$s and tagged %2$s.', 'portal' );
		} else {
			$meta_text = __( 'Posted in %1$s.', 'portal' );
		}
	} // end check for categories on this blog

	$meta = sprintf(
		$meta_text,
		$category_list,
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
	
	return apply_filters('portal_post_meta', $meta);
}

/**
 * Gets the URL that should be displayed when clicking on an image in the view image page.
 * 
 * @param null $post
 * @return string
 */
function portal_next_attachment_url($post = null){
	if(empty($post)){
		global $post;
	}
	
	/**
	 * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
	 * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
	 */
	$attachments = array_values( get_children( array(
		'post_parent'    => $post->post_parent,
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ID'
	) ) );
	foreach ( $attachments as $k => $attachment ) {
		if ( $attachment->ID == $post->ID )
			break;
	}
	$k++;
	// If there is more than 1 attachment in a gallery
	if ( count( $attachments ) > 1 ) {
		if ( isset( $attachments[ $k ] ) ){
			// get the URL of the next image attachment
			$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
		}
		else{
			// or get the URL of the first image attachment
			$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
		}
			
	}
	else {
		// or, if there's only 1 image, get the URL of the image
		$next_attachment_url = wp_get_attachment_url();
	}
	
	return $next_attachment_url;
}

function portal_entry_thumbnail(){
	// Change the content width to the size of the container
	global $content_width;
	$temp_content_width = $content_width;
	$content_width = 1170;

	global $post;

	switch(get_post_format()){
		case 'aside':
			break;
		case 'gallery':
			echo do_shortcode('[gallery type="slider"]');
			break;
		case 'video':
			echo $post->format_content;
			break;
		default :
			if(has_post_thumbnail()) {
				if(!is_single()) echo '<a href="'.get_permalink().'" title="'.esc_attr(get_the_title()).'">';
				the_post_thumbnail();
				if(!is_single()) echo '</a>';
			}
			break;
	}

	// Set the content width back
	$content_width = $temp_content_width;
}