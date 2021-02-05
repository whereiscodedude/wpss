<?php
/**
 * oEmbed API: Top-level oEmbed functionality
 *
 * @package WordPress
 * @subpackage oEmbed
 * @since 4.4.0
 */

/**
 * Registers an embed handler.
 *
 * Should probably only be used for sites that do not support oEmbed.
 *
 * @since 2.9.0
 *
 * @global WP_Embed $wp_embed
 *
 * @param string   $id       An internal ID/name for the handler. Needs to be unique.
 * @param string   $regex    The regex that will be used to see if this handler should be used for a URL.
 * @param callable $callback The callback function that will be called if the regex is matched.
 * @param int      $priority Optional. Used to specify the order in which the registered handlers will
 *                           be tested. Default 10.
 */
function wp_embed_register_handler( $id, $regex, $callback, $priority = 10 ) {
	global $wp_embed;
	$wp_embed->register_handler( $id, $regex, $callback, $priority );
}

/**
 * Unregisters a previously-registered embed handler.
 *
 * @since 2.9.0
 *
 * @global WP_Embed $wp_embed
 *
 * @param string $id       The handler ID that should be removed.
 * @param int    $priority Optional. The priority of the handler to be removed. Default 10.
 */
function wp_embed_unregister_handler( $id, $priority = 10 ) {
	global $wp_embed;
	$wp_embed->unregister_handler( $id, $priority );
}

/**
 * Creates default array of embed parameters.
 *
 * The width defaults to the content width as specified by the theme. If the
 * theme does not specify a content width, then 500px is used.
 *
 * The default height is 1.5 times the width, or 1000px, whichever is smaller.
 *
 * The {@see 'embed_defaults'} filter can be used to adjust either of these values.
 *
 * @since 2.9.0
 *
 * @global int $content_width
 *
 * @param string $url Optional. The URL that should be embedded. Default empty.
 * @return array {
 *     Indexed array of the embed width and height in pixels.
 *
 *     @type int $0 The embed width.
 *     @type int $1 The embed height.
 * }
 */
function wp_embed_defaults( $url = '' ) {
	if ( ! empty( $GLOBALS['content_width'] ) ) {
		$width = (int) $GLOBALS['content_width'];
	}

	if ( empty( $width ) ) {
		$width = 500;
	}

	$height = min( ceil( $width * 1.5 ), 1000 );

	/**
	 * Filters the default array of embed dimensions.
	 *
	 * @since 2.9.0
	 *
	 * @param array  $size {
	 *     Indexed array of the embed width and height in pixels.
	 *
	 *     @type int $0 The embed width.
	 *     @type int $1 The embed height.
	 * }
	 * @param string $url  The URL that should be embedded.
	 */
	return apply_filters( 'embed_defaults', compact( 'width', 'height' ), $url );
}

/**
 * Attempts to fetch the embed HTML for a provided URL using oEmbed.
 *
 * @since 2.9.0
 *
 * @see WP_oEmbed
 *
 * @param string $url  The URL that should be embedded.
 * @param array  $args Optional. Additional arguments and parameters for retrieving embed HTML.
 *                     Default empty.
 * @return string|false The embed HTML on success, false on failure.
 */
function wp_oembed_get( $url, $args = '' ) {
	$oembed = _wp_oembed_get_object();
	return $oembed->get_html( $url, $args );
}

/**
 * Returns the initialized WP_oEmbed object.
 *
 * @since 2.9.0
 * @access private
 *
 * @return WP_oEmbed object.
 */
function _wp_oembed_get_object() {
	static $wp_oembed = null;

	if ( is_null( $wp_oembed ) ) {
		$wp_oembed = new WP_oEmbed();
	}
	return $wp_oembed;
}

/**
 * Adds a URL format and oEmbed provider URL pair.
 *
 * @since 2.9.0
 *
 * @see WP_oEmbed
 *
 * @param string $format   The format of URL that this provider can handle. You can use asterisks
 *                         as wildcards.
 * @param string $provider The URL to the oEmbed provider.
 * @param bool   $regex    Optional. Whether the `$format` parameter is in a RegEx format. Default false.
 */
function wp_oembed_add_provider( $format, $provider, $regex = false ) {
	if ( did_action( 'plugins_loaded' ) ) {
		$oembed                       = _wp_oembed_get_object();
		$oembed->providers[ $format ] = array( $provider, $regex );
	} else {
		WP_oEmbed::_add_provider_early( $format, $provider, $regex );
	}
}

/**
 * Removes an oEmbed provider.
 *
 * @since 3.5.0
 *
 * @see WP_oEmbed
 *
 * @param string $format The URL format for the oEmbed provider to remove.
 * @return bool Was the provider removed successfully?
 */
function wp_oembed_remove_provider( $format ) {
	if ( did_action( 'plugins_loaded' ) ) {
		$oembed = _wp_oembed_get_object();

		if ( isset( $oembed->providers[ $format ] ) ) {
			unset( $oembed->providers[ $format ] );
			return true;
		}
	} else {
		WP_oEmbed::_remove_provider_early( $format );
	}

	return false;
}

/**
 * Determines if default embed handlers should be loaded.
 *
 * Checks to make sure that the embeds library hasn't already been loaded. If
 * it hasn't, then it will load the embeds library.
 *
 * @since 2.9.0
 *
 * @see wp_embed_register_handler()
 */
function wp_maybe_load_embeds() {
	/**
	 * Filters whether to load the default embed handlers.
	 *
	 * Returning a falsey value will prevent loading the default embed handlers.
	 *
	 * @since 2.9.0
	 *
	 * @param bool $maybe_load_embeds Whether to load the embeds library. Default true.
	 */
	if ( ! apply_filters( 'load_default_embeds', true ) ) {
		return;
	}

	wp_embed_register_handler( 'youtube_embed_url', '#https?://(www.)?youtube\.com/(?:v|embed)/([^/]+)#i', 'wp_embed_handler_youtube' );

	/**
	 * Filters the audio embed handler callback.
	 *
	 * @since 3.6.0
	 *
	 * @param callable $handler Audio embed handler callback function.
	 */
	wp_embed_register_handler( 'audio', '#^https?://.+?\.(' . join( '|', wp_get_audio_extensions() ) . ')$#i', apply_filters( 'wp_audio_embed_handler', 'wp_embed_handler_audio' ), 9999 );

	/**
	 * Filters the video embed handler callback.
	 *
	 * @since 3.6.0
	 *
	 * @param callable $handler Video embed handler callback function.
	 */
	wp_embed_register_handler( 'video', '#^https?://.+?\.(' . join( '|', wp_get_video_extensions() ) . ')$#i', apply_filters( 'wp_video_embed_handler', 'wp_embed_handler_video' ), 9999 );
}

/**
 * YouTube iframe embed handler callback.
 *
 * Catches YouTube iframe embed URLs that are not parsable by oEmbed but can be translated into a URL that is.
 *
 * @since 4.0.0
 *
 * @global WP_Embed $wp_embed
 *
 * @param array  $matches The RegEx matches from the provided regex when calling
 *                        wp_embed_register_handler().
 * @param array  $attr    Embed attributes.
 * @param string $url     The original URL that was matched by the regex.
 * @param array  $rawattr The original unmodified attributes.
 * @return string The embed HTML.
 */
function wp_embed_handler_youtube( $matches, $attr, $url, $rawattr ) {
	global $wp_embed;
	$embed = $wp_embed->autoembed( sprintf( 'https://youtube.com/watch?v=%s', urlencode( $matches[2] ) ) );

	/**
	 * Filters the YoutTube embed output.
	 *
	 * @since 4.0.0
	 *
	 * @see wp_embed_handler_youtube()
	 *
	 * @param string $embed   YouTube embed output.
	 * @param array  $attr    An array of embed attributes.
	 * @param string $url     The original URL that was matched by the regex.
	 * @param array  $rawattr The original unmodified attributes.
	 */
	return apply_filters( 'wp_embed_handler_youtube', $embed, $attr, $url, $rawattr );
}

/**
 * Audio embed handler callback.
 *
 * @since 3.6.0
 *
 * @param array  $matches The RegEx matches from the provided regex when calling wp_embed_register_handler().
 * @param array  $attr Embed attributes.
 * @param string $url The original URL that was matched by the regex.
 * @param array  $rawattr The original unmodified attributes.
 * @return string The embed HTML.
 */
function wp_embed_handler_audio( $matches, $attr, $url, $rawattr ) {
	$audio = sprintf( '[audio src="%s" /]', esc_url( $url ) );

	/**
	 * Filters the audio embed output.
	 *
	 * @since 3.6.0
	 *
	 * @param string $audio   Audio embed output.
	 * @param array  $attr    An array of embed attributes.
	 * @param string $url     The original URL that was matched by the regex.
	 * @param array  $rawattr The original unmodified attributes.
	 */
	return apply_filters( 'wp_embed_handler_audio', $audio, $attr, $url, $rawattr );
}

/**
 * Video embed handler callback.
 *
 * @since 3.6.0
 *
 * @param array  $matches The RegEx matches from the provided regex when calling wp_embed_register_handler().
 * @param array  $attr    Embed attributes.
 * @param string $url     The original URL that was matched by the regex.
 * @param array  $rawattr The original unmodified attributes.
 * @return string The embed HTML.
 */
function wp_embed_handler_video( $matches, $attr, $url, $rawattr ) {
	$dimensions = '';
	if ( ! empty( $rawattr['width'] ) && ! empty( $rawattr['height'] ) ) {
		$dimensions .= sprintf( 'width="%d" ', (int) $rawattr['width'] );
		$dimensions .= sprintf( 'height="%d" ', (int) $rawattr['height'] );
	}
	$video = sprintf( '[video %s src="%s" /]', $dimensions, esc_url( $url ) );

	/**
	 * Filters the video embed output.
	 *
	 * @since 3.6.0
	 *
	 * @param string $video   Video embed output.
	 * @param array  $attr    An array of embed attributes.
	 * @param string $url     The original URL that was matched by the regex.
	 * @param array  $rawattr The original unmodified attributes.
	 */
	return apply_filters( 'wp_embed_handler_video', $video, $attr, $url, $rawattr );
}

/**
 * Registers the oEmbed REST API route.
 *
 * @since 4.4.0
 */
function wp_oembed_register_route() {
	$controller = new WP_oEmbed_Controller();
	$controller->register_routes();
}

/**
 * Adds oEmbed discovery links in the website <head>.
 *
 * @since 4.4.0
 */
function wp_oembed_add_discovery_links() {
	$output = '';

	if ( is_singular() ) {
		$output .= '<link rel="alternate" type="application/json+oembed" href="' . esc_url( get_oembed_endpoint_url( get_permalink() ) ) . '" />' . "\n";

		if ( class_exists( 'SimpleXMLElement' ) ) {
			$output .= '<link rel="alternate" type="text/xml+oembed" href="' . esc_url( get_oembed_endpoint_url( get_permalink(), 'xml' ) ) . '" />' . "\n";
		}
	}

	/**
	 * Filters the oEmbed discovery links HTML.
	 *
	 * @since 4.4.0
	 *
	 * @param string $output HTML of the discovery links.
	 */
	echo apply_filters( 'oembed_discovery_links', $output );
}

/**
 * Adds the necessary JavaScript to communicate with the embedded iframes.
 *
 * @since 4.4.0
 */
function wp_oembed_add_host_js() {
	wp_enqueue_script( 'wp-embed' );
}

/**
 * Retrieves the URL to embed a specific post in an iframe.
 *
 * @since 4.4.0
 *
 * @param int|WP_Post $post Optional. Post ID or object. Defaults to the current post.
 * @return string|false The post embed URL on success, false if the post doesn't exist.
 */
function get_post_embed_url( $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	$embed_url     = trailingslashit( get_permalink( $post ) ) . user_trailingslashit( 'embed' );
	$path_conflict = get_page_by_path( str_replace( home_url(), '', $embed_url ), OBJECT, get_post_types( array( 'public' => true ) ) );

	if ( ! get_option( 'permalink_structure' ) || $path_conflict ) {
		$embed_url = add_query_arg( array( 'embed' => 'true' ), get_permalink( $post ) );
	}

	/**
	 * Filters the URL to embed a specific post.
	 *
	 * @since 4.4.0
	 *
	 * @param string  $embed_url The post embed URL.
	 * @param WP_Post $post      The corresponding post object.
	 */
	return esc_url_raw( apply_filters( 'post_embed_url', $embed_url, $post ) );
}

/**
 * Retrieves the oEmbed endpoint URL for a given permalink.
 *
 * Pass an empty string as the first argument to get the endpoint base URL.
 *
 * @since 4.4.0
 *
 * @param string $permalink Optional. The permalink used for the `url` query arg. Default empty.
 * @param string $format    Optional. The requested response format. Default 'json'.
 * @return string The oEmbed endpoint URL.
 */
function get_oembed_endpoint_url( $permalink = '', $format = 'json' ) {
	$url = rest_url( 'oembed/1.0/embed' );

	if ( '' !== $permalink ) {
		$url = add_query_arg(
			array(
				'url'    => urlencode( $permalink ),
				'format' => ( 'json' !== $format ) ? $format : false,
			),
			$url
		);
	}

	/**
	 * Filters the oEmbed endpoint URL.
	 *
	 * @since 4.4.0
	 *
	 * @param string $url       The URL to the oEmbed endpoint.
	 * @param string $permalink The permalink used for the `url` query arg.
	 * @param string $format    The requested response format.
	 */
	return apply_filters( 'oembed_endpoint_url', $url, $permalink, $format );
}

/**
 * Retrieves the embed code for a specific post.
 *
 * @since 4.4.0
 *
 * @param int         $width  The width for the response.
 * @param int         $height The height for the response.
 * @param int|WP_Post $post   Optional. Post ID or object. Default is global `$post`.
 * @return string|false Embed code on success, false if post doesn't exist.
 */
function get_post_embed_html( $width, $height, $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	$embed_url = get_post_embed_url( $post );

	$output = '<blockquote class="wp-embedded-content"><a href="' . esc_url( get_permalink( $post ) ) . '">' . get_the_title( $post ) . "</a></blockquote>\n";

	$output .= "<script type='text/javascript'>\n";
	$output .= "<!--//--><![CDATA[//><!--\n";
	if ( SCRIPT_DEBUG ) {
		$output .= file_get_contents( ABSPATH . WPINC . '/js/wp-embed.js' );
	} else {
		/*
		 * If you're looking at a src version of this file, you'll see an "include"
		 * statement below. This is used by the `npm run build` process to directly
		 * include a minified version of wp-embed.js, instead of using the
		 * file_get_contents() method from above.
		 *
		 * If you're looking at a build version of this file, you'll see a string of
		 * minified JavaScript. If you need to debug it, please turn on SCRIPT_DEBUG
		 * and edit wp-embed.js directly.
		 */
		$output .= <<<JS
		/*! This file is auto-generated */
		!function(c,d){"use strict";var e=!1,n=!1;if(d.querySelector)if(c.addEventListener)e=!0;if(c.wp=c.wp||{},!c.wp.receiveEmbedMessage)if(c.wp.receiveEmbedMessage=function(e){var t=e.data;if(t)if(t.secret||t.message||t.value)if(!/[^a-zA-Z0-9]/.test(t.secret)){for(var r,a,i,s=d.querySelectorAll('iframe[data-secret="'+t.secret+'"]'),n=d.querySelectorAll('blockquote[data-secret="'+t.secret+'"]'),o=0;o<n.length;o++)n[o].style.display="none";for(o=0;o<s.length;o++)if(r=s[o],e.source===r.contentWindow){if(r.removeAttribute("style"),"height"===t.message){if(1e3<(i=parseInt(t.value,10)))i=1e3;else if(~~i<200)i=200;r.height=i}if("link"===t.message)if(a=d.createElement("a"),i=d.createElement("a"),a.href=r.getAttribute("src"),i.href=t.value,i.host===a.host)if(d.activeElement===r)c.top.location.href=t.value}}},e)c.addEventListener("message",c.wp.receiveEmbedMessage,!1),d.addEventListener("DOMContentLoaded",t,!1),c.addEventListener("load",t,!1);function t(){if(!n){n=!0;for(var e,t,r=-1!==navigator.appVersion.indexOf("MSIE 10"),a=!!navigator.userAgent.match(/Trident.*rv:11\./),i=d.querySelectorAll("iframe.wp-embedded-content"),s=0;s<i.length;s++){if(!(e=i[s]).getAttribute("data-secret"))t=Math.random().toString(36).substr(2,10),e.src+="#?secret="+t,e.setAttribute("data-secret",t);if(r||a)(t=e.cloneNode(!0)).removeAttribute("security"),e.parentNode.replaceChild(t,e)}}}}(window,document);
JS;
	}
	$output .= "\n//--><!]]>";
	$output .= "\n</script>";

	$output .= sprintf(
		'<iframe sandbox="allow-scripts" security="restricted" src="%1$s" width="%2$d" height="%3$d" title="%4$s" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" class="wp-embedded-content"></iframe>',
		esc_url( $embed_url ),
		absint( $width ),
		absint( $height ),
		esc_attr(
			sprintf(
				/* translators: 1: Post title, 2: Site title. */
				__( '&#8220;%1$s&#8221; &#8212; %2$s' ),
				get_the_title( $post ),
				get_bloginfo( 'name' )
			)
		)
	);

	/**
	 * Filters the embed HTML output for a given post.
	 *
	 * @since 4.4.0
	 *
	 * @param string  $output The default iframe tag to display embedded content.
	 * @param WP_Post $post   Current post object.
	 * @param int     $width  Width of the response.
	 * @param int     $height Height of the response.
	 */
	return apply_filters( 'embed_html', $output, $post, $width, $height );
}

/**
 * Retrieves the oEmbed response data for a given post.
 *
 * @since 4.4.0
 *
 * @param WP_Post|int $post  Post object or ID.
 * @param int         $width The requested width.
 * @return array|false Response data on success, false if post doesn't exist.
 */
function get_oembed_response_data( $post, $width ) {
	$post  = get_post( $post );
	$width = absint( $width );

	if ( ! $post ) {
		return false;
	}

	if ( 'publish' !== get_post_status( $post ) ) {
		return false;
	}

	/**
	 * Filters the allowed minimum and maximum widths for the oEmbed response.
	 *
	 * @since 4.4.0
	 *
	 * @param array $min_max_width {
	 *     Minimum and maximum widths for the oEmbed response.
	 *
	 *     @type int $min Minimum width. Default 200.
	 *     @type int $max Maximum width. Default 600.
	 * }
	 */
	$min_max_width = apply_filters(
		'oembed_min_max_width',
		array(
			'min' => 200,
			'max' => 600,
		)
	);

	$width  = min( max( $min_max_width['min'], $width ), $min_max_width['max'] );
	$height = max( ceil( $width / 16 * 9 ), 200 );

	$data = array(
		'version'       => '1.0',
		'provider_name' => get_bloginfo( 'name' ),
		'provider_url'  => get_home_url(),
		'author_name'   => get_bloginfo( 'name' ),
		'author_url'    => get_home_url(),
		'title'         => get_the_title( $post ),
		'type'          => 'link',
	);

	$author = get_userdata( $post->post_author );

	if ( $author ) {
		$data['author_name'] = $author->display_name;
		$data['author_url']  = get_author_posts_url( $author->ID );
	}

	/**
	 * Filters the oEmbed response data.
	 *
	 * @since 4.4.0
	 *
	 * @param array   $data   The response data.
	 * @param WP_Post $post   The post object.
	 * @param int     $width  The requested width.
	 * @param int     $height The calculated height.
	 */
	return apply_filters( 'oembed_response_data', $data, $post, $width, $height );
}


/**
 * Retrieves the oEmbed response data for a given URL.
 *
 * @since 5.0.0
 *
 * @param string $url  The URL that should be inspected for discovery `<link>` tags.
 * @param array  $args oEmbed remote get arguments.
 * @return object|false oEmbed response data if the URL does belong to the current site. False otherwise.
 */
function get_oembed_response_data_for_url( $url, $args ) {
	$switched_blog = false;

	if ( is_multisite() ) {
		$url_parts = wp_parse_args(
			wp_parse_url( $url ),
			array(
				'host' => '',
				'path' => '/',
			)
		);

		$qv = array(
			'domain'                 => $url_parts['host'],
			'path'                   => '/',
			'update_site_meta_cache' => false,
		);

		// In case of subdirectory configs, set the path.
		if ( ! is_subdomain_install() ) {
			$path = explode( '/', ltrim( $url_parts['path'], '/' ) );
			$path = reset( $path );

			if ( $path ) {
				$qv['path'] = get_network()->path . $path . '/';
			}
		}

		$sites = get_sites( $qv );
		$site  = reset( $sites );

		// Do not allow embeds for deleted/archived/spam sites.
		if ( ! empty( $site->deleted ) || ! empty( $site->spam ) || ! empty( $site->archived ) ) {
			return false;
		}

		if ( $site && get_current_blog_id() !== (int) $site->blog_id ) {
			switch_to_blog( $site->blog_id );
			$switched_blog = true;
		}
	}

	$post_id = url_to_postid( $url );

	/** This filter is documented in wp-includes/class-wp-oembed-controller.php */
	$post_id = apply_filters( 'oembed_request_post_id', $post_id, $url );

	if ( ! $post_id ) {
		if ( $switched_blog ) {
			restore_current_blog();
		}

		return false;
	}

	$width = isset( $args['width'] ) ? $args['width'] : 0;

	$data = get_oembed_response_data( $post_id, $width );

	if ( $switched_blog ) {
		restore_current_blog();
	}

	return $data ? (object) $data : false;
}


/**
 * Filters the oEmbed response data to return an iframe embed code.
 *
 * @since 4.4.0
 *
 * @param array   $data   The response data.
 * @param WP_Post $post   The post object.
 * @param int     $width  The requested width.
 * @param int     $height The calculated height.
 * @return array The modified response data.
 */
function get_oembed_response_data_rich( $data, $post, $width, $height ) {
	$data['width']  = absint( $width );
	$data['height'] = absint( $height );
	$data['type']   = 'rich';
	$data['html']   = get_post_embed_html( $width, $height, $post );

	// Add post thumbnail to response if available.
	$thumbnail_id = false;

	if ( has_post_thumbnail( $post->ID ) ) {
		$thumbnail_id = get_post_thumbnail_id( $post->ID );
	}

	if ( 'attachment' === get_post_type( $post ) ) {
		if ( wp_attachment_is_image( $post ) ) {
			$thumbnail_id = $post->ID;
		} elseif ( wp_attachment_is( 'video', $post ) ) {
			$thumbnail_id = get_post_thumbnail_id( $post );
			$data['type'] = 'video';
		}
	}

	if ( $thumbnail_id ) {
		list( $thumbnail_url, $thumbnail_width, $thumbnail_height ) = wp_get_attachment_image_src( $thumbnail_id, array( $width, 99999 ) );
		$data['thumbnail_url']                                      = $thumbnail_url;
		$data['thumbnail_width']                                    = $thumbnail_width;
		$data['thumbnail_height']                                   = $thumbnail_height;
	}

	return $data;
}

/**
 * Ensures that the specified format is either 'json' or 'xml'.
 *
 * @since 4.4.0
 *
 * @param string $format The oEmbed response format. Accepts 'json' or 'xml'.
 * @return string The format, either 'xml' or 'json'. Default 'json'.
 */
function wp_oembed_ensure_format( $format ) {
	if ( ! in_array( $format, array( 'json', 'xml' ), true ) ) {
		return 'json';
	}

	return $format;
}

/**
 * Hooks into the REST API output to print XML instead of JSON.
 *
 * This is only done for the oEmbed API endpoint,
 * which supports both formats.
 *
 * @access private
 * @since 4.4.0
 *
 * @param bool                      $served  Whether the request has already been served.
 * @param WP_HTTP_ResponseInterface $result  Result to send to the client. Usually a WP_REST_Response.
 * @param WP_REST_Request           $request Request used to generate the response.
 * @param WP_REST_Server            $server  Server instance.
 * @return true
 */
function _oembed_rest_pre_serve_request( $served, $result, $request, $server ) {
	$params = $request->get_params();

	if ( '/oembed/1.0/embed' !== $request->get_route() || 'GET' !== $request->get_method() ) {
		return $served;
	}

	if ( ! isset( $params['format'] ) || 'xml' !== $params['format'] ) {
		return $served;
	}

	// Embed links inside the request.
	$data = $server->response_to_data( $result, false );

	if ( ! class_exists( 'SimpleXMLElement' ) ) {
		status_header( 501 );
		die( get_status_header_desc( 501 ) );
	}

	$result = _oembed_create_xml( $data );

	// Bail if there's no XML.
	if ( ! $result ) {
		status_header( 501 );
		return get_status_header_desc( 501 );
	}

	if ( ! headers_sent() ) {
		$server->send_header( 'Content-Type', 'text/xml; charset=' . get_option( 'blog_charset' ) );
	}

	echo $result;

	return true;
}

/**
 * Creates an XML string from a given array.
 *
 * @since 4.4.0
 * @access private
 *
 * @param array            $data The original oEmbed response data.
 * @param SimpleXMLElement $node Optional. XML node to append the result to recursively.
 * @return string|false XML string on success, false on error.
 */
function _oembed_create_xml( $data, $node = null ) {
	if ( ! is_array( $data ) || empty( $data ) ) {
		return false;
	}

	if ( null === $node ) {
		$node = new SimpleXMLElement( '<oembed></oembed>' );
	}

	foreach ( $data as $key => $value ) {
		if ( is_numeric( $key ) ) {
			$key = 'oembed';
		}

		if ( is_array( $value ) ) {
			$item = $node->addChild( $key );
			_oembed_create_xml( $value, $item );
		} else {
			$node->addChild( $key, esc_html( $value ) );
		}
	}

	return $node->asXML();
}

/**
 * Filters the given oEmbed HTML to make sure iframes have a title attribute.
 *
 * @since 5.2.0
 *
 * @param string $result The oEmbed HTML result.
 * @param object $data   A data object result from an oEmbed provider.
 * @param string $url    The URL of the content to be embedded.
 * @return string The filtered oEmbed result.
 */
function wp_filter_oembed_iframe_title_attribute( $result, $data, $url ) {
	if ( false === $result || ! in_array( $data->type, array( 'rich', 'video' ), true ) ) {
		return $result;
	}

	$title = ! empty( $data->title ) ? $data->title : '';

	$pattern = '`<iframe([^>]*)>`i';
	if ( preg_match( $pattern, $result, $matches ) ) {
		$attrs = wp_kses_hair( $matches[1], wp_allowed_protocols() );

		foreach ( $attrs as $attr => $item ) {
			$lower_attr = strtolower( $attr );
			if ( $lower_attr === $attr ) {
				continue;
			}
			if ( ! isset( $attrs[ $lower_attr ] ) ) {
				$attrs[ $lower_attr ] = $item;
				unset( $attrs[ $attr ] );
			}
		}
	}

	if ( ! empty( $attrs['title']['value'] ) ) {
		$title = $attrs['title']['value'];
	}

	/**
	 * Filters the title attribute of the given oEmbed HTML iframe.
	 *
	 * @since 5.2.0
	 *
	 * @param string $title  The title attribute.
	 * @param string $result The oEmbed HTML result.
	 * @param object $data   A data object result from an oEmbed provider.
	 * @param string $url    The URL of the content to be embedded.
	 */
	$title = apply_filters( 'oembed_iframe_title_attribute', $title, $result, $data, $url );

	if ( '' === $title ) {
		return $result;
	}

	if ( isset( $attrs['title'] ) ) {
		unset( $attrs['title'] );
		$attr_string = join( ' ', wp_list_pluck( $attrs, 'whole' ) );
		$result      = str_replace( $matches[0], '<iframe ' . trim( $attr_string ) . '>', $result );
	}
	return str_ireplace( '<iframe ', sprintf( '<iframe title="%s" ', esc_attr( $title ) ), $result );
}


/**
 * Filters the given oEmbed HTML.
 *
 * If the `$url` isn't on the trusted providers list,
 * we need to filter the HTML heavily for security.
 *
 * Only filters 'rich' and 'video' response types.
 *
 * @since 4.4.0
 *
 * @param string $result The oEmbed HTML result.
 * @param object $data   A data object result from an oEmbed provider.
 * @param string $url    The URL of the content to be embedded.
 * @return string The filtered and sanitized oEmbed result.
 */
function wp_filter_oembed_result( $result, $data, $url ) {
	if ( false === $result || ! in_array( $data->type, array( 'rich', 'video' ), true ) ) {
		return $result;
	}

	$wp_oembed = _wp_oembed_get_object();

	// Don't modify the HTML for trusted providers.
	if ( false !== $wp_oembed->get_provider( $url, array( 'discover' => false ) ) ) {
		return $result;
	}

	$allowed_html = array(
		'a'          => array(
			'href' => true,
		),
		'blockquote' => array(),
		'iframe'     => array(
			'src'          => true,
			'width'        => true,
			'height'       => true,
			'frameborder'  => true,
			'marginwidth'  => true,
			'marginheight' => true,
			'scrolling'    => true,
			'title'        => true,
		),
	);

	$html = wp_kses( $result, $allowed_html );

	preg_match( '|(<blockquote>.*?</blockquote>)?.*(<iframe.*?></iframe>)|ms', $html, $content );
	// We require at least the iframe to exist.
	if ( empty( $content[2] ) ) {
		return false;
	}
	$html = $content[1] . $content[2];

	preg_match( '/ src=([\'"])(.*?)\1/', $html, $results );

	if ( ! empty( $results ) ) {
		$secret = wp_generate_password( 10, false );

		$url = esc_url( "{$results[2]}#?secret=$secret" );
		$q   = $results[1];

		$html = str_replace( $results[0], ' src=' . $q . $url . $q . ' data-secret=' . $q . $secret . $q, $html );
		$html = str_replace( '<blockquote', "<blockquote data-secret=\"$secret\"", $html );
	}

	$allowed_html['blockquote']['data-secret'] = true;
	$allowed_html['iframe']['data-secret']     = true;

	$html = wp_kses( $html, $allowed_html );

	if ( ! empty( $content[1] ) ) {
		// We have a blockquote to fall back on. Hide the iframe by default.
		$html = str_replace( '<iframe', '<iframe style="position: absolute; clip: rect(1px, 1px, 1px, 1px);"', $html );
		$html = str_replace( '<blockquote', '<blockquote class="wp-embedded-content"', $html );
	}

	$html = str_ireplace( '<iframe', '<iframe class="wp-embedded-content" sandbox="allow-scripts" security="restricted"', $html );

	return $html;
}

/**
 * Filters the string in the 'more' link displayed after a trimmed excerpt.
 *
 * Replaces '[...]' (appended to automatically generated excerpts) with an
 * ellipsis and a "Continue reading" link in the embed template.
 *
 * @since 4.4.0
 *
 * @param string $more_string Default 'more' string.
 * @return string 'Continue reading' link prepended with an ellipsis.
 */
function wp_embed_excerpt_more( $more_string ) {
	if ( ! is_embed() ) {
		return $more_string;
	}

	$link = sprintf(
		'<a href="%1$s" class="wp-embed-more" target="_top">%2$s</a>',
		esc_url( get_permalink() ),
		/* translators: %s: Post title. */
		sprintf( __( 'Continue reading %s' ), '<span class="screen-reader-text">' . get_the_title() . '</span>' )
	);
	return ' &hellip; ' . $link;
}

/**
 * Displays the post excerpt for the embed template.
 *
 * Intended to be used in 'The Loop'.
 *
 * @since 4.4.0
 */
function the_excerpt_embed() {
	$output = get_the_excerpt();

	/**
	 * Filters the post excerpt for the embed template.
	 *
	 * @since 4.4.0
	 *
	 * @param string $output The current post excerpt.
	 */
	echo apply_filters( 'the_excerpt_embed', $output );
}

/**
 * Filters the post excerpt for the embed template.
 *
 * Shows players for video and audio attachments.
 *
 * @since 4.4.0
 *
 * @param string $content The current post excerpt.
 * @return string The modified post excerpt.
 */
function wp_embed_excerpt_attachment( $content ) {
	if ( is_attachment() ) {
		return prepend_attachment( '' );
	}

	return $content;
}

/**
 * Enqueue embed iframe default CSS and JS & fire do_action('enqueue_embed_scripts')
 *
 * Enqueue PNG fallback CSS for embed iframe for legacy versions of IE.
 *
 * Allows plugins to queue scripts for the embed iframe end using wp_enqueue_script().
 * Runs first in oembed_head().
 *
 * @since 4.4.0
 */
function enqueue_embed_scripts() {
	wp_enqueue_style( 'wp-embed-template-ie' );

	/**
	 * Fires when scripts and styles are enqueued for the embed iframe.
	 *
	 * @since 4.4.0
	 */
	do_action( 'enqueue_embed_scripts' );
}

/**
 * Prints the CSS in the embed iframe header.
 *
 * @since 4.4.0
 */
function print_embed_styles() {
	$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';
	?>
	<style<?php echo $type_attr; ?>>
	<?php
	if ( SCRIPT_DEBUG ) {
		readfile( ABSPATH . WPINC . '/css/wp-embed-template.css' );
	} else {
		/*
		 * If you're looking at a src version of this file, you'll see an "include"
		 * statement below. This is used by the `npm run build` process to directly
		 * include a minified version of wp-oembed-embed.css, instead of using the
		 * readfile() method from above.
		 *
		 * If you're looking at a build version of this file, you'll see a string of
		 * minified CSS. If you need to debug it, please turn on SCRIPT_DEBUG
		 * and edit wp-embed-template.css directly.
		 */
		?>
			/*! This file is auto-generated */
			body,html{padding:0;margin:0}body{font-family:sans-serif}.screen-reader-text{border:0;clip:rect(1px,1px,1px,1px);-webkit-clip-path:inset(50%);clip-path:inset(50%);height:1px;margin:-1px;overflow:hidden;padding:0;position:absolute;width:1px;word-wrap:normal!important}.dashicons{display:inline-block;width:20px;height:20px;background-color:transparent;background-repeat:no-repeat;background-size:20px;background-position:center;transition:background .1s ease-in;position:relative;top:5px}.dashicons-no{background-image:url("data:image/svg+xml;charset=utf8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%2020%2020%27%3E%3Cpath%20d%3D%27M15.55%2013.7l-2.19%202.06-3.42-3.65-3.64%203.43-2.06-2.18%203.64-3.43-3.42-3.64%202.18-2.06%203.43%203.64%203.64-3.42%202.05%202.18-3.64%203.43z%27%20fill%3D%27%23fff%27%2F%3E%3C%2Fsvg%3E")}.dashicons-admin-comments{background-image:url("data:image/svg+xml;charset=utf8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%2020%2020%27%3E%3Cpath%20d%3D%27M5%202h9q.82%200%201.41.59T16%204v7q0%20.82-.59%201.41T14%2013h-2l-5%205v-5H5q-.82%200-1.41-.59T3%2011V4q0-.82.59-1.41T5%202z%27%20fill%3D%27%2382878c%27%2F%3E%3C%2Fsvg%3E")}.wp-embed-comments a:hover .dashicons-admin-comments{background-image:url("data:image/svg+xml;charset=utf8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%2020%2020%27%3E%3Cpath%20d%3D%27M5%202h9q.82%200%201.41.59T16%204v7q0%20.82-.59%201.41T14%2013h-2l-5%205v-5H5q-.82%200-1.41-.59T3%2011V4q0-.82.59-1.41T5%202z%27%20fill%3D%27%230073aa%27%2F%3E%3C%2Fsvg%3E")}.dashicons-share{background-image:url("data:image/svg+xml;charset=utf8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%2020%2020%27%3E%3Cpath%20d%3D%27M14.5%2012q1.24%200%202.12.88T17.5%2015t-.88%202.12-2.12.88-2.12-.88T11.5%2015q0-.34.09-.69l-4.38-2.3Q6.32%2013%205%2013q-1.24%200-2.12-.88T2%2010t.88-2.12T5%207q1.3%200%202.21.99l4.38-2.3q-.09-.35-.09-.69%200-1.24.88-2.12T14.5%202t2.12.88T17.5%205t-.88%202.12T14.5%208q-1.3%200-2.21-.99l-4.38%202.3Q8%209.66%208%2010t-.09.69l4.38%202.3q.89-.99%202.21-.99z%27%20fill%3D%27%2382878c%27%2F%3E%3C%2Fsvg%3E");display:none}.js .dashicons-share{display:inline-block}.wp-embed-share-dialog-open:hover .dashicons-share{background-image:url("data:image/svg+xml;charset=utf8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%2020%2020%27%3E%3Cpath%20d%3D%27M14.5%2012q1.24%200%202.12.88T17.5%2015t-.88%202.12-2.12.88-2.12-.88T11.5%2015q0-.34.09-.69l-4.38-2.3Q6.32%2013%205%2013q-1.24%200-2.12-.88T2%2010t.88-2.12T5%207q1.3%200%202.21.99l4.38-2.3q-.09-.35-.09-.69%200-1.24.88-2.12T14.5%202t2.12.88T17.5%205t-.88%202.12T14.5%208q-1.3%200-2.21-.99l-4.38%202.3Q8%209.66%208%2010t-.09.69l4.38%202.3q.89-.99%202.21-.99z%27%20fill%3D%27%230073aa%27%2F%3E%3C%2Fsvg%3E")}.wp-embed{padding:25px;font-size:14px;font-weight:400;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;line-height:1.5;color:#82878c;background:#fff;border:1px solid #e5e5e5;box-shadow:0 1px 1px rgba(0,0,0,.05);overflow:auto;zoom:1}.wp-embed a{color:#82878c;text-decoration:none}.wp-embed a:hover{text-decoration:underline}.wp-embed-featured-image{margin-bottom:20px}.wp-embed-featured-image img{width:100%;height:auto;border:none}.wp-embed-featured-image.square{float:left;max-width:160px;margin-right:20px}.wp-embed p{margin:0}p.wp-embed-heading{margin:0 0 15px;font-weight:600;font-size:22px;line-height:1.3}.wp-embed-heading a{color:#32373c}.wp-embed .wp-embed-more{color:#b4b9be}.wp-embed-footer{display:table;width:100%;margin-top:30px}.wp-embed-site-icon{position:absolute;top:50%;left:0;transform:translateY(-50%);height:25px;width:25px;border:0}.wp-embed-site-title{font-weight:600;line-height:1.78571428}.wp-embed-site-title a{position:relative;display:inline-block;padding-left:35px}.wp-embed-meta,.wp-embed-site-title{display:table-cell}.wp-embed-meta{text-align:right;white-space:nowrap;vertical-align:middle}.wp-embed-comments,.wp-embed-share{display:inline}.wp-embed-meta a:hover{text-decoration:none;color:#0073aa}.wp-embed-comments a{line-height:1.78571428;display:inline-block}.wp-embed-comments+.wp-embed-share{margin-left:10px}.wp-embed-share-dialog{position:absolute;top:0;left:0;right:0;bottom:0;background-color:#222;background-color:rgba(10,10,10,.9);color:#fff;opacity:1;transition:opacity .25s ease-in-out}.wp-embed-share-dialog.hidden{opacity:0;visibility:hidden}.wp-embed-share-dialog-close,.wp-embed-share-dialog-open{margin:-8px 0 0;padding:0;background:0 0;border:none;cursor:pointer;outline:0}.wp-embed-share-dialog-close .dashicons,.wp-embed-share-dialog-open .dashicons{padding:4px}.wp-embed-share-dialog-open .dashicons{top:8px}.wp-embed-share-dialog-close:focus .dashicons,.wp-embed-share-dialog-open:focus .dashicons{box-shadow:0 0 0 1px #5b9dd9,0 0 2px 1px rgba(30,140,190,.8);border-radius:100%}.wp-embed-share-dialog-close{position:absolute;top:20px;right:20px;font-size:22px}.wp-embed-share-dialog-close:hover{text-decoration:none}.wp-embed-share-dialog-close .dashicons{height:24px;width:24px;background-size:24px}.wp-embed-share-dialog-content{height:100%;transform-style:preserve-3d;overflow:hidden}.wp-embed-share-dialog-text{margin-top:25px;padding:20px}.wp-embed-share-tabs{margin:0 0 20px;padding:0;list-style:none}.wp-embed-share-tab-button{display:inline-block}.wp-embed-share-tab-button button{margin:0;padding:0;border:none;background:0 0;font-size:16px;line-height:1.3;color:#aaa;cursor:pointer;transition:color .1s ease-in}.wp-embed-share-tab-button [aria-selected=true]{color:#fff}.wp-embed-share-tab-button button:hover{color:#fff}.wp-embed-share-tab-button+.wp-embed-share-tab-button{margin:0 0 0 10px;padding:0 0 0 11px;border-left:1px solid #aaa}.wp-embed-share-tab[aria-hidden=true]{display:none}p.wp-embed-share-description{margin:0;font-size:14px;line-height:1;font-style:italic;color:#aaa}.wp-embed-share-input{box-sizing:border-box;width:100%;border:none;height:28px;margin:0 0 10px 0;padding:0 5px;font-size:14px;font-weight:400;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;line-height:1.5;resize:none;cursor:text}textarea.wp-embed-share-input{height:72px}html[dir=rtl] .wp-embed-featured-image.square{float:right;margin-right:0;margin-left:20px}html[dir=rtl] .wp-embed-site-title a{padding-left:0;padding-right:35px}html[dir=rtl] .wp-embed-site-icon{margin-right:0;margin-left:10px;left:auto;right:0}html[dir=rtl] .wp-embed-meta{text-align:left}html[dir=rtl] .wp-embed-share{margin-left:0;margin-right:10px}html[dir=rtl] .wp-embed-share-dialog-close{right:auto;left:20px}html[dir=rtl] .wp-embed-share-tab-button+.wp-embed-share-tab-button{margin:0 10px 0 0;padding:0 11px 0 0;border-left:none;border-right:1px solid #aaa}
		<?php
	}
	?>
	</style>
	<?php
}

/**
 * Prints the JavaScript in the embed iframe header.
 *
 * @since 4.4.0
 */
function print_embed_scripts() {
	$type_attr = current_theme_supports( 'html5', 'script' ) ? '' : ' type="text/javascript"';
	?>
	<script<?php echo $type_attr; ?>>
	<?php
	if ( SCRIPT_DEBUG ) {
		readfile( ABSPATH . WPINC . '/js/wp-embed-template.js' );
	} else {
		/*
		 * If you're looking at a src version of this file, you'll see an "include"
		 * statement below. This is used by the `npm run build` process to directly
		 * include a minified version of wp-embed-template.js, instead of using the
		 * readfile() method from above.
		 *
		 * If you're looking at a build version of this file, you'll see a string of
		 * minified JavaScript. If you need to debug it, please turn on SCRIPT_DEBUG
		 * and edit wp-embed-template.js directly.
		 */
		?>
			/*! This file is auto-generated */
			!function(u,c){"use strict";var r,t,e,i=c.querySelector&&u.addEventListener,b=!1;function f(e,t){u.parent.postMessage({message:e,value:t,secret:r},"*")}function n(){if(!b){b=!0;var e,r=c.querySelector(".wp-embed-share-dialog"),t=c.querySelector(".wp-embed-share-dialog-open"),i=c.querySelector(".wp-embed-share-dialog-close"),n=c.querySelectorAll(".wp-embed-share-input"),a=c.querySelectorAll(".wp-embed-share-tab-button button"),o=c.querySelector(".wp-embed-featured-image img");if(n)for(e=0;e<n.length;e++)n[e].addEventListener("click",function(e){e.target.select()});if(t&&t.addEventListener("click",function(){r.className=r.className.replace("hidden",""),c.querySelector('.wp-embed-share-tab-button [aria-selected="true"]').focus()}),i&&i.addEventListener("click",function(){l()}),a)for(e=0;e<a.length;e++)a[e].addEventListener("click",s),a[e].addEventListener("keydown",d);c.addEventListener("keydown",function(e){var t;27===e.keyCode&&-1===r.className.indexOf("hidden")?l():9===e.keyCode&&(t=e,e=c.querySelector('.wp-embed-share-tab-button [aria-selected="true"]'),i!==t.target||t.shiftKey?e===t.target&&t.shiftKey&&(i.focus(),t.preventDefault()):(e.focus(),t.preventDefault()))},!1),u.self!==u.top&&(f("height",Math.ceil(c.body.getBoundingClientRect().height)),o&&o.addEventListener("load",function(){f("height",Math.ceil(c.body.getBoundingClientRect().height))}),c.addEventListener("click",function(e){var t=e.target;(t=(t.hasAttribute("href")?t:t.parentElement).getAttribute("href"))&&(f("link",t),e.preventDefault())}))}function l(){r.className+=" hidden",c.querySelector(".wp-embed-share-dialog-open").focus()}function s(e){var t=c.querySelector('.wp-embed-share-tab-button [aria-selected="true"]');t.setAttribute("aria-selected","false"),c.querySelector("#"+t.getAttribute("aria-controls")).setAttribute("aria-hidden","true"),e.target.setAttribute("aria-selected","true"),c.querySelector("#"+e.target.getAttribute("aria-controls")).setAttribute("aria-hidden","false")}function d(e){var t,r=e.target,i=r.parentElement.previousElementSibling,n=r.parentElement.nextElementSibling;if(37===e.keyCode)t=i;else{if(39!==e.keyCode)return!1;t=n}(t="rtl"===c.documentElement.getAttribute("dir")?t===i?n:i:t)&&(t=t.firstElementChild,r.setAttribute("tabindex","-1"),r.setAttribute("aria-selected",!1),c.querySelector("#"+r.getAttribute("aria-controls")).setAttribute("aria-hidden","true"),t.setAttribute("tabindex","0"),t.setAttribute("aria-selected","true"),t.focus(),c.querySelector("#"+t.getAttribute("aria-controls")).setAttribute("aria-hidden","false"))}}i&&(!function e(){u.self===u.top||r||(r=u.location.hash.replace(/.*secret=([\d\w]{10}).*/,"$1"),clearTimeout(t),t=setTimeout(function(){e()},100))}(),c.documentElement.className=c.documentElement.className.replace(/\bno-js\b/,"")+" js",c.addEventListener("DOMContentLoaded",n,!1),u.addEventListener("load",n,!1),u.addEventListener("resize",function(){u.self!==u.top&&(clearTimeout(e),e=setTimeout(function(){f("height",Math.ceil(c.body.getBoundingClientRect().height))},100))},!1))}(window,document);
		<?php
	}
	?>
	</script>
	<?php
}

/**
 * Prepare the oembed HTML to be displayed in an RSS feed.
 *
 * @since 4.4.0
 * @access private
 *
 * @param string $content The content to filter.
 * @return string The filtered content.
 */
function _oembed_filter_feed_content( $content ) {
	return str_replace( '<iframe class="wp-embedded-content" sandbox="allow-scripts" security="restricted" style="position: absolute; clip: rect(1px, 1px, 1px, 1px);"', '<iframe class="wp-embedded-content" sandbox="allow-scripts" security="restricted"', $content );
}

/**
 * Prints the necessary markup for the embed comments button.
 *
 * @since 4.4.0
 */
function print_embed_comments_button() {
	if ( is_404() || ! ( get_comments_number() || comments_open() ) ) {
		return;
	}
	?>
	<div class="wp-embed-comments">
		<a href="<?php comments_link(); ?>" target="_top">
			<span class="dashicons dashicons-admin-comments"></span>
			<?php
			printf(
				/* translators: %s: Number of comments. */
				_n(
					'%s <span class="screen-reader-text">Comment</span>',
					'%s <span class="screen-reader-text">Comments</span>',
					get_comments_number()
				),
				number_format_i18n( get_comments_number() )
			);
			?>
		</a>
	</div>
	<?php
}

/**
 * Prints the necessary markup for the embed sharing button.
 *
 * @since 4.4.0
 */
function print_embed_sharing_button() {
	if ( is_404() ) {
		return;
	}
	?>
	<div class="wp-embed-share">
		<button type="button" class="wp-embed-share-dialog-open" aria-label="<?php esc_attr_e( 'Open sharing dialog' ); ?>">
			<span class="dashicons dashicons-share"></span>
		</button>
	</div>
	<?php
}

/**
 * Prints the necessary markup for the embed sharing dialog.
 *
 * @since 4.4.0
 */
function print_embed_sharing_dialog() {
	if ( is_404() ) {
		return;
	}
	?>
	<div class="wp-embed-share-dialog hidden" role="dialog" aria-label="<?php esc_attr_e( 'Sharing options' ); ?>">
		<div class="wp-embed-share-dialog-content">
			<div class="wp-embed-share-dialog-text">
				<ul class="wp-embed-share-tabs" role="tablist">
					<li class="wp-embed-share-tab-button wp-embed-share-tab-button-wordpress" role="presentation">
						<button type="button" role="tab" aria-controls="wp-embed-share-tab-wordpress" aria-selected="true" tabindex="0"><?php esc_html_e( 'WordPress Embed' ); ?></button>
					</li>
					<li class="wp-embed-share-tab-button wp-embed-share-tab-button-html" role="presentation">
						<button type="button" role="tab" aria-controls="wp-embed-share-tab-html" aria-selected="false" tabindex="-1"><?php esc_html_e( 'HTML Embed' ); ?></button>
					</li>
				</ul>
				<div id="wp-embed-share-tab-wordpress" class="wp-embed-share-tab" role="tabpanel" aria-hidden="false">
					<input type="text" value="<?php the_permalink(); ?>" class="wp-embed-share-input" aria-describedby="wp-embed-share-description-wordpress" tabindex="0" readonly/>

					<p class="wp-embed-share-description" id="wp-embed-share-description-wordpress">
						<?php _e( 'Copy and paste this URL into your WordPress site to embed' ); ?>
					</p>
				</div>
				<div id="wp-embed-share-tab-html" class="wp-embed-share-tab" role="tabpanel" aria-hidden="true">
					<textarea class="wp-embed-share-input" aria-describedby="wp-embed-share-description-html" tabindex="0" readonly><?php echo esc_textarea( get_post_embed_html( 600, 400 ) ); ?></textarea>

					<p class="wp-embed-share-description" id="wp-embed-share-description-html">
						<?php _e( 'Copy and paste this code into your site to embed' ); ?>
					</p>
				</div>
			</div>

			<button type="button" class="wp-embed-share-dialog-close" aria-label="<?php esc_attr_e( 'Close sharing dialog' ); ?>">
				<span class="dashicons dashicons-no"></span>
			</button>
		</div>
	</div>
	<?php
}

/**
 * Prints the necessary markup for the site title in an embed template.
 *
 * @since 4.5.0
 */
function the_embed_site_title() {
	$site_title = sprintf(
		'<a href="%s" target="_top"><img src="%s" srcset="%s 2x" width="32" height="32" alt="" class="wp-embed-site-icon"/><span>%s</span></a>',
		esc_url( home_url() ),
		esc_url( get_site_icon_url( 32, includes_url( 'images/w-logo-blue.png' ) ) ),
		esc_url( get_site_icon_url( 64, includes_url( 'images/w-logo-blue.png' ) ) ),
		esc_html( get_bloginfo( 'name' ) )
	);

	$site_title = '<div class="wp-embed-site-title">' . $site_title . '</div>';

	/**
	 * Filters the site title HTML in the embed footer.
	 *
	 * @since 4.4.0
	 *
	 * @param string $site_title The site title HTML.
	 */
	echo apply_filters( 'embed_site_title_html', $site_title );
}

/**
 * Filters the oEmbed result before any HTTP requests are made.
 *
 * If the URL belongs to the current site, the result is fetched directly instead of
 * going through the oEmbed discovery process.
 *
 * @since 4.5.3
 *
 * @param null|string $result The UNSANITIZED (and potentially unsafe) HTML that should be used to embed. Default null.
 * @param string      $url    The URL that should be inspected for discovery `<link>` tags.
 * @param array       $args   oEmbed remote get arguments.
 * @return null|string The UNSANITIZED (and potentially unsafe) HTML that should be used to embed.
 *                     Null if the URL does not belong to the current site.
 */
function wp_filter_pre_oembed_result( $result, $url, $args ) {
	$data = get_oembed_response_data_for_url( $url, $args );

	if ( $data ) {
		return _wp_oembed_get_object()->data2html( $data, $url );
	}

	return $result;
}
