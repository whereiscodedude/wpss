<?php
/**
 * Page layout with image and text.
 */
return array(
	'title'      => __( 'Page layout with image and text', 'twentytwentytwo' ),
	'categories' => array( 'twentytwentytwo-pages' ),
	'content'    => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"8rem","bottom":"2rem","right":"max(1.25rem, 5vw)","left":"max(1.25rem, 5vw)"}}},"layout":{"inherit":true}} -->
				<div class="wp-block-group alignfull" style="padding-top:8rem;padding-right:max(1.25rem, 5vw);padding-bottom:2rem;padding-left:max(1.25rem, 5vw)"><!-- wp:heading {"align":"wide","style":{"typography":{"fontSize":"clamp(4rem, 8vw, 7.5rem)","lineHeight":"1.15","fontWeight":"300"}}} -->
					<h2 class="alignwide" style="font-size:clamp(4rem, 8vw, 7.5rem);font-weight:300;line-height:1.15">' . wp_kses_post( __( '<em>Watching Birds </em><br><em>in the Garden</em>', 'twentytwentytwo' ) ) . '</h2>
					<!-- /wp:heading --></div>
					<!-- /wp:group -->

					<!-- wp:image {"align":"full","style":{"color":{}}} -->
					<figure class="wp-block-image alignfull"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/flight-path-on-transparent-b.png" alt="' . esc_attr_x( 'TBD', 'Short for to be determined', 'twentytwentytwo' ) . '"/></figure>
					<!-- /wp:image -->

					<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"2rem","bottom":"8rem","right":"max(1.25rem, 5vw)","left":"max(1.25rem, 5vw)"}}},"layout":{"inherit":true}} -->
					<div class="wp-block-group alignfull" style="padding-top:2rem;padding-right:max(1.25rem, 5vw);padding-bottom:8rem;padding-left:max(1.25rem, 5vw)">
					<!-- wp:columns {"align":"wide"} -->
					<div class="wp-block-columns alignwide"><!-- wp:column {"verticalAlignment":"bottom","style":{"spacing":{"padding":{"bottom":"1em"}}}} -->
					<div class="wp-block-column is-vertically-aligned-bottom" style="padding-bottom:1em"><!-- wp:site-logo {"width":60} /--></div>
					<!-- /wp:column -->

					<!-- wp:column {"verticalAlignment":"bottom"} -->
					<div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:paragraph -->
					<p>' . wp_kses_post( __( 'Oh hello. My name’s Angelo, and I operate this blog. I was born in Portland, but I currently live in upstate New York. You may recognize me from publications with names like <a href="#">Eagle Beagle</a> and <a href="#">Mourning Dive</a>. I write for a living.<br><br>I usually use this blog to catalog extensive lists of birds and other things that I find interesting. If you find an error with one of my lists, please keep it to yourself.<br><br>If that’s not your cup of tea, <a href="#">I definitely recommend this tea</a>. It’s my favorite.', 'twentytwentytwo' ) ) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns --></div>
					<!-- /wp:group -->',
);
