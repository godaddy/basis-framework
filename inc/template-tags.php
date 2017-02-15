<?php
/**
 * Custom template tags for this theme.
 *
 * @package  Functions
 * @category Core
 * @author   GoDaddy
 * @since    1.0.0
 */

/**
 * Display a custom logo.
 *
 * @since 1.0.0
 */
function primer_the_custom_logo() {

	/**
	 * For backwards compatibility prior to WordPress 4.5.
	 *
	 * @link  https://developer.wordpress.org/reference/functions/the_custom_logo/
	 * @since 1.0.0
	 */
	if ( function_exists( 'the_custom_logo' ) ) {

		the_custom_logo();

		return;

	}

	$custom_logo_id = get_theme_mod( 'custom_logo' );

	if ( ! $custom_logo_id && ! is_customize_preview() ) {

		return;

	}

	$args = array(
		'class'    => 'custom-logo',
		'itemprop' => 'logo',
	);

	printf(
		'<a href="%1$s" class="custom-logo-link" %2$s>%3$s</a>',
		esc_url( home_url( '/' ) ),
		$custom_logo_id ? 'rel="home" itemprop="url"' : 'style="display:none;"',
		$custom_logo_id ? wp_get_attachment_image( $custom_logo_id, 'full', false, $args ) : '<img class="custom-logo"/>'
	);

}

/**
 * Display the site title.
 *
 * @since 1.0.0
 */
function primer_the_site_title() {

	$html = sprintf(
		'<h1 class="site-title"><a href="%s" rel="home">%s</a></h1>',
		esc_url( home_url( '/' ) ),
		get_bloginfo( 'name' )
	);

	/**
	 * Filter the site title HTML.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	echo (string) apply_filters( 'primer_the_site_title', $html );

}

/**
 * Display the site description.
 *
 * @since 1.0.0
 */
function primer_the_site_description() {

	$html = sprintf(
		'<div class="site-description">%s</div>',
		get_bloginfo( 'description' )
	);

	/**
	 * Filter the site description HTML.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	echo (string) apply_filters( 'primer_the_site_description', $html );

}

/**
 * Display a page title.
 *
 * @since 1.0.0
 */
function primer_the_page_title() {

	if ( $title = primer_get_the_page_title() ) {

		echo $title; // xss ok

	}

}

/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @global WP_Query $wp_query
 * @since  1.0.0
 */
function primer_paging_nav() {

	global $wp_query;

	if ( ! isset( $wp_query->max_num_pages ) || $wp_query->max_num_pages < 2 ) {

		return;

	}

	?>
	<nav class="navigation paging-navigation">

		<h2 class="screen-reader-text"><?php esc_html_e( 'Posts navigation', 'primer' ); ?></h2>

		<div class="nav-links">

		<?php if ( get_next_posts_link() ) : ?>

			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'primer' ) ); ?></div>

		<?php endif; ?>

		<?php if ( get_previous_posts_link() ) : ?>

			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'primer' ) ); ?></div>

		<?php endif; ?>

		</div><!-- .nav-links -->

	</nav><!-- .navigation -->
	<?php

}

/**
 * Display navigation to next/previous post when applicable.
 *
 * @uses the_post_navigation
 * @uses primer_post_nav_text
 *
 * @since  1.0.0
 */
function primer_post_nav() {

	/**
	 * Filter the next/prev navigation text
	 *
	 * @since NEXT
	 *
	 * @var array
	 */
	$navigation_text = (array) apply_filters( 'primer_post_nav_text', [
		'next_text' => '%title &rarr;',
		'prev_text' => '&larr; %title',
	] );

	the_post_navigation( array(
		'next_text'          => "<div class='nav-next'>{$navigation_text['next_text']}</div>",
		'prev_text'          => "<div class='nav-previous'>{$navigation_text['prev_text']}</div>",
		'screen_reader_text' => esc_html__( 'Post navigation', 'primer' ),
	) );

}

/**
 * Prints HTML with meta information for the current post-date/time and author.
 *
 * @since 1.0.0
 */
function primer_posted_on() {

	$time = sprintf(
		'<time class="entry-date published" datetime="%s">%s</time>',
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);

	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {

		$time = sprintf(
			'<time class="updated" datetime="%s">%s</time>',
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

	}

	printf(
		'<span class="posted-on"><a href="%s" rel="bookmark">%s</a><span>',
		get_permalink(),
		$time // xss ok
	);

}

/**
 * Prints the post format for the current post.
 *
 * @since 1.0.0
 */
function primer_post_format() {

	$format = get_post_format();
	$format = empty( $format ) ? 'standard' : $format;

	printf( '<span class="post-format">%s</span>', esc_html( $format ) );

}

/**
 * Display very simple breadcrumbs.
 *
 * Adapted from Christoph Weil's Really Simple Breadcrumb plugin.
 *
 * @global WP_Post $post
 * @link   https://wordpress.org/plugins/really-simple-breadcrumb/
 * @since  1.0.0
 */
function primer_breadcrumbs() {

	global $post;

	$separator = ' <span class="sep"></span> ';

	echo '<div class="breadcrumbs">';

	if ( ! is_front_page() ) {

		printf(
			'<a href="%s">%s</a>%s',
			esc_url( home_url( '/' ) ),
			esc_html( get_bloginfo( 'name' ) ),
			$separator // xss ok
		);

		if ( 'page' === get_option( 'show_on_front' ) ) {

			printf(
				'<a href="%s">%s</a>%s',
				esc_url( primer_get_posts_url() ),
				esc_html__( 'Blog', 'primer' ),
				$separator // xss ok
			);

		}

		if ( is_category() || is_single() ) {

			the_category( ', ' );

			if ( is_single() ) {

				echo $separator; // xss ok

				the_title();

			}

		} elseif ( is_page() && $post->post_parent ) {

			$home = get_post( get_option( 'page_on_front' ) );

			for ( $i = count( $post->ancestors ) - 1; $i >= 0; $i-- ) {

				if ( $home->ID !== $post->ancestors[ $i ] ) {

					printf(
						'<a href="%s">%s</a>%s',
						esc_url( get_permalink( $post->ancestors[ $i ] ) ),
						esc_html( get_the_title( $post->ancestors[ $i ] ) ),
						$separator // xss ok
					);

				}

			}

			the_title();

		} elseif ( is_page() ) {

			the_title();

		} elseif ( is_404() ) {

			echo '404';

		}

	} else {

		bloginfo( 'name' );

	}

	echo '</div>';

}
