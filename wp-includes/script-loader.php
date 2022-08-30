<?php
/**
 * WordPress scripts and styles default loader.
 *
 * Several constants are used to manage the loading, concatenating and compression of scripts and CSS:
 * define('SCRIPT_DEBUG', true); loads the development (non-minified) versions of all scripts and CSS, and disables compression and concatenation,
 * define('CONCATENATE_SCRIPTS', false); disables compression and concatenation of scripts and CSS,
 * define('COMPRESS_SCRIPTS', false); disables compression of scripts,
 * define('COMPRESS_CSS', false); disables compression of CSS,
 * define('ENFORCE_GZIP', true); forces gzip for compression (default is deflate).
 *
 * The globals $concatenate_scripts, $compress_scripts and $compress_css can be set by plugins
 * to temporarily override the above settings. Also a compression test is run once and the result is saved
 * as option 'can_compress_scripts' (0/1). The test will run again if that option is deleted.
 *
 * @package WordPress
 */

/** WordPress Dependency Class */
require ABSPATH . WPINC . '/class-wp-dependency.php';

/** WordPress Dependencies Class */
require ABSPATH . WPINC . '/class.wp-dependencies.php';

/** WordPress Scripts Class */
require ABSPATH . WPINC . '/class.wp-scripts.php';

/** WordPress Scripts Functions */
require ABSPATH . WPINC . '/functions.wp-scripts.php';

/** WordPress Styles Class */
require ABSPATH . WPINC . '/class.wp-styles.php';

/** WordPress Styles Functions */
require ABSPATH . WPINC . '/functions.wp-styles.php';

/**
 * Registers TinyMCE scripts.
 *
 * @since 5.0.0
 *
 * @param WP_Scripts $scripts            WP_Scripts object.
 * @param bool       $force_uncompressed Whether to forcibly prevent gzip compression. Default false.
 */
function wp_register_tinymce_scripts( $scripts, $force_uncompressed = false ) {
	global $tinymce_version, $concatenate_scripts, $compress_scripts;
	$suffix     = wp_scripts_get_suffix();
	$dev_suffix = wp_scripts_get_suffix( 'dev' );

	script_concat_settings();

	$compressed = $compress_scripts && $concatenate_scripts && isset( $_SERVER['HTTP_ACCEPT_ENCODING'] )
		&& false !== stripos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' ) && ! $force_uncompressed;

	// Load tinymce.js when running from /src, otherwise load wp-tinymce.js.gz (in production)
	// or tinymce.min.js (when SCRIPT_DEBUG is true).
	if ( $compressed ) {
		$scripts->add( 'wp-tinymce', includes_url( 'js/tinymce/' ) . 'wp-tinymce.js', array(), $tinymce_version );
	} else {
		$scripts->add( 'wp-tinymce-root', includes_url( 'js/tinymce/' ) . "tinymce$dev_suffix.js", array(), $tinymce_version );
		$scripts->add( 'wp-tinymce', includes_url( 'js/tinymce/' ) . "plugins/compat3x/plugin$dev_suffix.js", array( 'wp-tinymce-root' ), $tinymce_version );
	}

	$scripts->add( 'wp-tinymce-lists', includes_url( "js/tinymce/plugins/lists/plugin$suffix.js" ), array( 'wp-tinymce' ), $tinymce_version );
}

/**
 * Registers all the WordPress vendor scripts that are in the standardized
 * `js/dist/vendor/` location.
 *
 * For the order of `$scripts->add` see `wp_default_scripts`.
 *
 * @since 5.0.0
 *
 * @param WP_Scripts $scripts WP_Scripts object.
 */
function wp_default_packages_vendor( $scripts ) {
	global $wp_locale;

	$suffix = wp_scripts_get_suffix();

	$vendor_scripts = array(
		'react'       => array( 'wp-polyfill' ),
		'react-dom'   => array( 'react' ),
		'regenerator-runtime',
		'moment',
		'lodash',
		'wp-polyfill-fetch',
		'wp-polyfill-formdata',
		'wp-polyfill-node-contains',
		'wp-polyfill-url',
		'wp-polyfill-dom-rect',
		'wp-polyfill-element-closest',
		'wp-polyfill-object-fit',
		'wp-polyfill' => array( 'regenerator-runtime' ),
	);

	$vendor_scripts_versions = array(
		'react'                       => '16.13.1',
		'react-dom'                   => '16.13.1',
		'regenerator-runtime'         => '0.13.7',
		'moment'                      => '2.29.1',
		'lodash'                      => '4.17.19',
		'wp-polyfill-fetch'           => '3.0.0',
		'wp-polyfill-formdata'        => '4.0.0',
		'wp-polyfill-node-contains'   => '3.105.0',
		'wp-polyfill-url'             => '3.6.4',
		'wp-polyfill-dom-rect'        => '3.104.0',
		'wp-polyfill-element-closest' => '2.0.2',
		'wp-polyfill-object-fit'      => '2.3.5',
		'wp-polyfill'                 => '3.15.0',
	);

	foreach ( $vendor_scripts as $handle => $dependencies ) {
		if ( is_string( $dependencies ) ) {
			$handle       = $dependencies;
			$dependencies = array();
		}

		$path    = "/wp-includes/js/dist/vendor/$handle$suffix.js";
		$version = $vendor_scripts_versions[ $handle ];

		$scripts->add( $handle, $path, $dependencies, $version, 1 );
	}

	did_action( 'init' ) && $scripts->add_inline_script( 'lodash', 'window.lodash = _.noConflict();' );

	did_action( 'init' ) && $scripts->add_inline_script(
		'moment',
		sprintf(
			"moment.updateLocale( '%s', %s );",
			get_user_locale(),
			wp_json_encode(
				array(
					'months'         => array_values( $wp_locale->month ),
					'monthsShort'    => array_values( $wp_locale->month_abbrev ),
					'weekdays'       => array_values( $wp_locale->weekday ),
					'weekdaysShort'  => array_values( $wp_locale->weekday_abbrev ),
					'week'           => array(
						'dow' => (int) get_option( 'start_of_week', 0 ),
					),
					'longDateFormat' => array(
						'LT'   => get_option( 'time_format', __( 'g:i a' ) ),
						'LTS'  => null,
						'L'    => null,
						'LL'   => get_option( 'date_format', __( 'F j, Y' ) ),
						'LLL'  => __( 'F j, Y g:i a' ),
						'LLLL' => null,
					),
				)
			)
		),
		'after'
	);
}

/**
 * Returns contents of an inline script used in appending polyfill scripts for
 * browsers which fail the provided tests. The provided array is a mapping from
 * a condition to verify feature support to its polyfill script handle.
 *
 * @since 5.0.0
 *
 * @param WP_Scripts $scripts WP_Scripts object.
 * @param array      $tests   Features to detect.
 * @return string Conditional polyfill inline script.
 */
function wp_get_script_polyfill( $scripts, $tests ) {
	$polyfill = '';
	foreach ( $tests as $test => $handle ) {
		if ( ! array_key_exists( $handle, $scripts->registered ) ) {
			continue;
		}

		$src = $scripts->registered[ $handle ]->src;
		$ver = $scripts->registered[ $handle ]->ver;

		if ( ! preg_match( '|^(https?:)?//|', $src ) && ! ( $scripts->content_url && 0 === strpos( $src, $scripts->content_url ) ) ) {
			$src = $scripts->base_url . $src;
		}

		if ( ! empty( $ver ) ) {
			$src = add_query_arg( 'ver', $ver, $src );
		}

		/** This filter is documented in wp-includes/class.wp-scripts.php */
		$src = esc_url( apply_filters( 'script_loader_src', $src, $handle ) );

		if ( ! $src ) {
			continue;
		}

		$polyfill .= (
			// Test presence of feature...
			'( ' . $test . ' ) || ' .
			/*
			 * ...appending polyfill on any failures. Cautious viewers may balk
			 * at the `document.write`. Its caveat of synchronous mid-stream
			 * blocking write is exactly the behavior we need though.
			 */
			'document.write( \'<script src="' .
			$src .
			'"></scr\' + \'ipt>\' );'
		);
	}

	return $polyfill;
}

/**
 * Registers all the WordPress packages scripts that are in the standardized
 * `js/dist/` location.
 *
 * For the order of `$scripts->add` see `wp_default_scripts`.
 *
 * @since 5.0.0
 *
 * @param WP_Scripts $scripts WP_Scripts object.
 */
function wp_default_packages_scripts( $scripts ) {
	$suffix = wp_scripts_get_suffix();

	// Expects multidimensional array like:
	//	'a11y.js' => array('dependencies' => array(...), 'version' => '...'),
	//	'annotations.js' => array('dependencies' => array(...), 'version' => '...'),
	//	'api-fetch.js' => array(...
	$assets = include ABSPATH . WPINC . '/assets/script-loader-packages.php';

	foreach ( $assets as $package_name => $package_data ) {
		$basename = basename( $package_name, '.js' );
		$handle   = 'wp-' . $basename;
		$path     = "/wp-includes/js/dist/{$basename}{$suffix}.js";

		if ( ! empty( $package_data['dependencies'] ) ) {
			$dependencies = $package_data['dependencies'];
		} else {
			$dependencies = array();
		}

		// Add dependencies that cannot be detected and generated by build tools.
		switch ( $handle ) {
			case 'wp-block-library':
				array_push( $dependencies, 'editor' );
				break;
			case 'wp-edit-post':
				array_push( $dependencies, 'media-models', 'media-views', 'postbox', 'wp-dom-ready' );
				break;
		}

		$scripts->add( $handle, $path, $dependencies, $package_data['version'], 1 );

		if ( in_array( 'wp-i18n', $dependencies, true ) ) {
			$scripts->set_translations( $handle );
		}

		/*
		 * Manually set the text direction localization after wp-i18n is printed.
		 * This ensures that wp.i18n.isRTL() returns true in RTL languages.
		 * We cannot use $scripts->set_translations( 'wp-i18n' ) to do this
		 * because WordPress prints a script's translations *before* the script,
		 * which means, in the case of wp-i18n, that wp.i18n.setLocaleData()
		 * is called before wp.i18n is defined.
		 */
		if ( 'wp-i18n' === $handle ) {
			$ltr    = _x( 'ltr', 'text direction' );
			$script = sprintf( "wp.i18n.setLocaleData( { 'text direction\u0004ltr': [ '%s' ] } );", $ltr );
			$scripts->add_inline_script( $handle, $script, 'after' );
		}
	}
}

/**
 * Adds inline scripts required for the WordPress JavaScript packages.
 *
 * @since 5.0.0
 *
 * @param WP_Scripts $scripts WP_Scripts object.
 */
function wp_default_packages_inline_scripts( $scripts ) {
	global $wp_locale;

	if ( isset( $scripts->registered['wp-api-fetch'] ) ) {
		$scripts->registered['wp-api-fetch']->deps[] = 'wp-hooks';
	}
	$scripts->add_inline_script(
		'wp-api-fetch',
		sprintf(
			'wp.apiFetch.use( wp.apiFetch.createRootURLMiddleware( "%s" ) );',
			esc_url_raw( get_rest_url() )
		),
		'after'
	);
	$scripts->add_inline_script(
		'wp-api-fetch',
		implode(
			"\n",
			array(
				sprintf(
					'wp.apiFetch.nonceMiddleware = wp.apiFetch.createNonceMiddleware( "%s" );',
					( wp_installing() && ! is_multisite() ) ? '' : wp_create_nonce( 'wp_rest' )
				),
				'wp.apiFetch.use( wp.apiFetch.nonceMiddleware );',
				'wp.apiFetch.use( wp.apiFetch.mediaUploadMiddleware );',
				sprintf(
					'wp.apiFetch.nonceEndpoint = "%s";',
					admin_url( 'admin-ajax.php?action=rest-nonce' )
				),
			)
		),
		'after'
	);
	$scripts->add_inline_script(
		'wp-data',
		implode(
			"\n",
			array(
				'( function() {',
				'	var userId = ' . get_current_user_ID() . ';',
				'	var storageKey = "WP_DATA_USER_" + userId;',
				'	wp.data',
				'		.use( wp.data.plugins.persistence, { storageKey: storageKey } );',
				'	wp.data.plugins.persistence.__unstableMigrate( { storageKey: storageKey } );',
				'} )();',
			)
		)
	);

	// Calculate the timezone abbr (EDT, PST) if possible.
	$timezone_string = get_option( 'timezone_string', 'UTC' );
	$timezone_abbr   = '';

	if ( ! empty( $timezone_string ) ) {
		$timezone_date = new DateTime( null, new DateTimeZone( $timezone_string ) );
		$timezone_abbr = $timezone_date->format( 'T' );
	}

	$scripts->add_inline_script(
		'wp-date',
		sprintf(
			'wp.date.setSettings( %s );',
			wp_json_encode(
				array(
					'l10n'     => array(
						'locale'        => get_user_locale(),
						'months'        => array_values( $wp_locale->month ),
						'monthsShort'   => array_values( $wp_locale->month_abbrev ),
						'weekdays'      => array_values( $wp_locale->weekday ),
						'weekdaysShort' => array_values( $wp_locale->weekday_abbrev ),
						'meridiem'      => (object) $wp_locale->meridiem,
						'relative'      => array(
							/* translators: %s: Duration. */
							'future' => __( '%s from now' ),
							/* translators: %s: Duration. */
							'past'   => __( '%s ago' ),
						),
					),
					'formats'  => array(
						/* translators: Time format, see https://www.php.net/manual/datetime.format.php */
						'time'                => get_option( 'time_format', __( 'g:i a' ) ),
						/* translators: Date format, see https://www.php.net/manual/datetime.format.php */
						'date'                => get_option( 'date_format', __( 'F j, Y' ) ),
						/* translators: Date/Time format, see https://www.php.net/manual/datetime.format.php */
						'datetime'            => __( 'F j, Y g:i a' ),
						/* translators: Abbreviated date/time format, see https://www.php.net/manual/datetime.format.php */
						'datetimeAbbreviated' => __( 'M j, Y g:i a' ),
					),
					'timezone' => array(
						'offset' => get_option( 'gmt_offset', 0 ),
						'string' => $timezone_string,
						'abbr'   => $timezone_abbr,
					),
				)
			)
		),
		'after'
	);

	// Loading the old editor and its config to ensure the classic block works as expected.
	$scripts->add_inline_script(
		'editor',
		'window.wp.oldEditor = window.wp.editor;',
		'after'
	);

	/*
	 * wp-editor module is exposed as window.wp.editor.
	 * Problem: there is quite some code expecting window.wp.oldEditor object available under window.wp.editor.
	 * Solution: fuse the two objects together to maintain backward compatibility.
	 * For more context, see https://github.com/WordPress/gutenberg/issues/33203.
	 */
	$scripts->add_inline_script(
		'wp-editor',
		'Object.assign( window.wp.editor, window.wp.oldEditor );',
		'after'
	);
}

/**
 * Adds inline scripts required for the TinyMCE in the block editor.
 *
 * These TinyMCE init settings are used to extend and override the default settings
 * from `_WP_Editors::default_settings()` for the Classic block.
 *
 * @since 5.0.0
 *
 * @global WP_Scripts $wp_scripts
 */
function wp_tinymce_inline_scripts() {
	global $wp_scripts;

	/** This filter is documented in wp-includes/class-wp-editor.php */
	$editor_settings = apply_filters( 'wp_editor_settings', array( 'tinymce' => true ), 'classic-block' );

	$tinymce_plugins = array(
		'charmap',
		'colorpicker',
		'hr',
		'lists',
		'media',
		'paste',
		'tabfocus',
		'textcolor',
		'fullscreen',
		'wordpress',
		'wpautoresize',
		'wpeditimage',
		'wpemoji',
		'wpgallery',
		'wplink',
		'wpdialogs',
		'wptextpattern',
		'wpview',
	);

	/** This filter is documented in wp-includes/class-wp-editor.php */
	$tinymce_plugins = apply_filters( 'tiny_mce_plugins', $tinymce_plugins, 'classic-block' );
	$tinymce_plugins = array_unique( $tinymce_plugins );

	$disable_captions = false;
	// Runs after `tiny_mce_plugins` but before `mce_buttons`.
	/** This filter is documented in wp-admin/includes/media.php */
	if ( apply_filters( 'disable_captions', '' ) ) {
		$disable_captions = true;
	}

	$toolbar1 = array(
		'formatselect',
		'bold',
		'italic',
		'bullist',
		'numlist',
		'blockquote',
		'alignleft',
		'aligncenter',
		'alignright',
		'link',
		'unlink',
		'wp_more',
		'spellchecker',
		'wp_add_media',
		'wp_adv',
	);

	/** This filter is documented in wp-includes/class-wp-editor.php */
	$toolbar1 = apply_filters( 'mce_buttons', $toolbar1, 'classic-block' );

	$toolbar2 = array(
		'strikethrough',
		'hr',
		'forecolor',
		'pastetext',
		'removeformat',
		'charmap',
		'outdent',
		'indent',
		'undo',
		'redo',
		'wp_help',
	);

	/** This filter is documented in wp-includes/class-wp-editor.php */
	$toolbar2 = apply_filters( 'mce_buttons_2', $toolbar2, 'classic-block' );
	/** This filter is documented in wp-includes/class-wp-editor.php */
	$toolbar3 = apply_filters( 'mce_buttons_3', array(), 'classic-block' );
	/** This filter is documented in wp-includes/class-wp-editor.php */
	$toolbar4 = apply_filters( 'mce_buttons_4', array(), 'classic-block' );
	/** This filter is documented in wp-includes/class-wp-editor.php */
	$external_plugins = apply_filters( 'mce_external_plugins', array(), 'classic-block' );

	$tinymce_settings = array(
		'plugins'              => implode( ',', $tinymce_plugins ),
		'toolbar1'             => implode( ',', $toolbar1 ),
		'toolbar2'             => implode( ',', $toolbar2 ),
		'toolbar3'             => implode( ',', $toolbar3 ),
		'toolbar4'             => implode( ',', $toolbar4 ),
		'external_plugins'     => wp_json_encode( $external_plugins ),
		'classic_block_editor' => true,
	);

	if ( $disable_captions ) {
		$tinymce_settings['wpeditimage_disable_captions'] = true;
	}

	if ( ! empty( $editor_settings['tinymce'] ) && is_array( $editor_settings['tinymce'] ) ) {
		array_merge( $tinymce_settings, $editor_settings['tinymce'] );
	}

	/** This filter is documented in wp-includes/class-wp-editor.php */
	$tinymce_settings = apply_filters( 'tiny_mce_before_init', $tinymce_settings, 'classic-block' );

	// Do "by hand" translation from PHP array to js object.
	// Prevents breakage in some custom settings.
	$init_obj = '';
	foreach ( $tinymce_settings as $key => $value ) {
		if ( is_bool( $value ) ) {
			$val       = $value ? 'true' : 'false';
			$init_obj .= $key . ':' . $val . ',';
			continue;
		} elseif ( ! empty( $value ) && is_string( $value ) && (
			( '{' === $value[0] && '}' === $value[ strlen( $value ) - 1 ] ) ||
			( '[' === $value[0] && ']' === $value[ strlen( $value ) - 1 ] ) ||
			preg_match( '/^\(?function ?\(/', $value ) ) ) {
			$init_obj .= $key . ':' . $value . ',';
			continue;
		}
		$init_obj .= $key . ':"' . $value . '",';
	}

	$init_obj = '{' . trim( $init_obj, ' ,' ) . '}';

	$script = 'window.wpEditorL10n = {
		tinymce: {
			baseURL: ' . wp_json_encode( includes_url( 'js/tinymce' ) ) . ',
			suffix: ' . ( SCRIPT_DEBUG ? '""' : '".min"' ) . ',
			settings: ' . $init_obj . ',
		}
	}';

	$wp_scripts->add_inline_script( 'wp-block-library', $script, 'before' );
}

/**
 * Registers all the WordPress packages scripts.
 *
 * @since 5.0.0
 *
 * @param WP_Scripts $scripts WP_Scripts object.
 */
function wp_default_packages( $scripts ) {
	wp_default_packages_vendor( $scripts );
	wp_register_tinymce_scripts( $scripts );
	wp_default_packages_scripts( $scripts );

	if ( did_action( 'init' ) ) {
		wp_default_packages_inline_scripts( $scripts );
	}
}

/**
 * Returns the suffix that can be used for the scripts.
 *
 * There are two suffix types, the normal one and the dev suffix.
 *
 * @since 5.0.0
 *
 * @param string $type The type of suffix to retrieve.
 * @return string The script suffix.
 */
function wp_scripts_get_suffix( $type = '' ) {
	static $suffixes;

	if ( null === $suffixes ) {
		// Include an unmodified $wp_version.
		require ABSPATH . WPINC . '/version.php';

		$develop_src = false !== strpos( $wp_version, '-src' );

		if ( ! defined( 'SCRIPT_DEBUG' ) ) {
			define( 'SCRIPT_DEBUG', $develop_src );
		}
		$suffix     = SCRIPT_DEBUG ? '' : '.min';
		$dev_suffix = $develop_src ? '' : '.min';

		$suffixes = array(
			'suffix'     => $suffix,
			'dev_suffix' => $dev_suffix,
		);
	}

	if ( 'dev' === $type ) {
		return $suffixes['dev_suffix'];
	}

	return $suffixes['suffix'];
}

/**
 * Register all WordPress scripts.
 *
 * Localizes some of them.
 * args order: `$scripts->add( 'handle', 'url', 'dependencies', 'query-string', 1 );`
 * when last arg === 1 queues the script for the footer
 *
 * @since 2.6.0
 *
 * @param WP_Scripts $scripts WP_Scripts object.
 */
function wp_default_scripts( $scripts ) {
	$suffix     = wp_scripts_get_suffix();
	$dev_suffix = wp_scripts_get_suffix( 'dev' );
	$guessurl   = site_url();

	if ( ! $guessurl ) {
		$guessed_url = true;
		$guessurl    = wp_guess_url();
	}

	$scripts->base_url        = $guessurl;
	$scripts->content_url     = defined( 'WP_CONTENT_URL' ) ? WP_CONTENT_URL : '';
	$scripts->default_version = get_bloginfo( 'version' );
	$scripts->default_dirs    = array( '/wp-admin/js/', '/wp-includes/js/' );

	$scripts->add( 'utils', "/wp-includes/js/utils$suffix.js" );
	did_action( 'init' ) && $scripts->localize(
		'utils',
		'userSettings',
		array(
			'url'    => (string) SITECOOKIEPATH,
			'uid'    => (string) get_current_user_id(),
			'time'   => (string) time(),
			'secure' => (string) ( 'https' === parse_url( site_url(), PHP_URL_SCHEME ) ),
		)
	);

	$scripts->add( 'common', "/wp-admin/js/common$suffix.js", array( 'jquery', 'hoverIntent', 'utils' ), false, 1 );
	$scripts->set_translations( 'common' );

	$scripts->add( 'wp-sanitize', "/wp-includes/js/wp-sanitize$suffix.js", array(), false, 1 );

	$scripts->add( 'sack', "/wp-includes/js/tw-sack$suffix.js", array(), '1.6.1', 1 );

	$scripts->add( 'quicktags', "/wp-includes/js/quicktags$suffix.js", array(), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'quicktags',
		'quicktagsL10n',
		array(
			'closeAllOpenTags'      => __( 'Close all open tags' ),
			'closeTags'             => __( 'close tags' ),
			'enterURL'              => __( 'Enter the URL' ),
			'enterImageURL'         => __( 'Enter the URL of the image' ),
			'enterImageDescription' => __( 'Enter a description of the image' ),
			'textdirection'         => __( 'text direction' ),
			'toggleTextdirection'   => __( 'Toggle Editor Text Direction' ),
			'dfw'                   => __( 'Distraction-free writing mode' ),
			'strong'                => __( 'Bold' ),
			'strongClose'           => __( 'Close bold tag' ),
			'em'                    => __( 'Italic' ),
			'emClose'               => __( 'Close italic tag' ),
			'link'                  => __( 'Insert link' ),
			'blockquote'            => __( 'Blockquote' ),
			'blockquoteClose'       => __( 'Close blockquote tag' ),
			'del'                   => __( 'Deleted text (strikethrough)' ),
			'delClose'              => __( 'Close deleted text tag' ),
			'ins'                   => __( 'Inserted text' ),
			'insClose'              => __( 'Close inserted text tag' ),
			'image'                 => __( 'Insert image' ),
			'ul'                    => __( 'Bulleted list' ),
			'ulClose'               => __( 'Close bulleted list tag' ),
			'ol'                    => __( 'Numbered list' ),
			'olClose'               => __( 'Close numbered list tag' ),
			'li'                    => __( 'List item' ),
			'liClose'               => __( 'Close list item tag' ),
			'code'                  => __( 'Code' ),
			'codeClose'             => __( 'Close code tag' ),
			'more'                  => __( 'Insert Read More tag' ),
		)
	);

	$scripts->add( 'colorpicker', "/wp-includes/js/colorpicker$suffix.js", array( 'prototype' ), '3517m' );

	$scripts->add( 'editor', "/wp-admin/js/editor$suffix.js", array( 'utils', 'jquery' ), false, 1 );

	$scripts->add( 'clipboard', "/wp-includes/js/clipboard$suffix.js", array(), false, 1 );

	$scripts->add( 'wp-ajax-response', "/wp-includes/js/wp-ajax-response$suffix.js", array( 'jquery' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'wp-ajax-response',
		'wpAjax',
		array(
			'noPerm' => __( 'Sorry, you are not allowed to do that.' ),
			'broken' => __( 'Something went wrong.' ),
		)
	);

	$scripts->add( 'wp-api-request', "/wp-includes/js/api-request$suffix.js", array( 'jquery' ), false, 1 );
	// `wpApiSettings` is also used by `wp-api`, which depends on this script.
	did_action( 'init' ) && $scripts->localize(
		'wp-api-request',
		'wpApiSettings',
		array(
			'root'          => esc_url_raw( get_rest_url() ),
			'nonce'         => ( wp_installing() && ! is_multisite() ) ? '' : wp_create_nonce( 'wp_rest' ),
			'versionString' => 'wp/v2/',
		)
	);

	$scripts->add( 'wp-pointer', "/wp-includes/js/wp-pointer$suffix.js", array( 'jquery-ui-core' ), false, 1 );
	$scripts->set_translations( 'wp-pointer' );

	$scripts->add( 'autosave', "/wp-includes/js/autosave$suffix.js", array( 'heartbeat' ), false, 1 );

	$scripts->add( 'heartbeat', "/wp-includes/js/heartbeat$suffix.js", array( 'jquery', 'wp-hooks' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'heartbeat',
		'heartbeatSettings',
		/**
		 * Filters the Heartbeat settings.
		 *
		 * @since 3.6.0
		 *
		 * @param array $settings Heartbeat settings array.
		 */
		apply_filters( 'heartbeat_settings', array() )
	);

	$scripts->add( 'wp-auth-check', "/wp-includes/js/wp-auth-check$suffix.js", array( 'heartbeat' ), false, 1 );
	$scripts->set_translations( 'wp-auth-check' );

	$scripts->add( 'wp-lists', "/wp-includes/js/wp-lists$suffix.js", array( 'wp-ajax-response', 'jquery-color' ), false, 1 );

	// WordPress no longer uses or bundles Prototype or script.aculo.us. These are now pulled from an external source.
	$scripts->add( 'prototype', 'https://ajax.googleapis.com/ajax/libs/prototype/1.7.1.0/prototype.js', array(), '1.7.1' );
	$scripts->add( 'scriptaculous-root', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/scriptaculous.js', array( 'prototype' ), '1.9.0' );
	$scripts->add( 'scriptaculous-builder', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/builder.js', array( 'scriptaculous-root' ), '1.9.0' );
	$scripts->add( 'scriptaculous-dragdrop', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/dragdrop.js', array( 'scriptaculous-builder', 'scriptaculous-effects' ), '1.9.0' );
	$scripts->add( 'scriptaculous-effects', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/effects.js', array( 'scriptaculous-root' ), '1.9.0' );
	$scripts->add( 'scriptaculous-slider', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/slider.js', array( 'scriptaculous-effects' ), '1.9.0' );
	$scripts->add( 'scriptaculous-sound', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/sound.js', array( 'scriptaculous-root' ), '1.9.0' );
	$scripts->add( 'scriptaculous-controls', 'https://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/controls.js', array( 'scriptaculous-root' ), '1.9.0' );
	$scripts->add( 'scriptaculous', false, array( 'scriptaculous-dragdrop', 'scriptaculous-slider', 'scriptaculous-controls' ) );

	// Not used in core, replaced by Jcrop.js.
	$scripts->add( 'cropper', '/wp-includes/js/crop/cropper.js', array( 'scriptaculous-dragdrop' ) );

	// jQuery.
	// The unminified jquery.js and jquery-migrate.js are included to facilitate debugging.
	$scripts->add( 'jquery', false, array( 'jquery-core', 'jquery-migrate' ), '3.6.0' );
	$scripts->add( 'jquery-core', "/wp-includes/js/jquery/jquery$suffix.js", array(), '3.6.0' );
	$scripts->add( 'jquery-migrate', "/wp-includes/js/jquery/jquery-migrate$suffix.js", array(), '3.3.2' );

	// Full jQuery UI.
	// The build process in 1.12.1 has changed significantly.
	// In order to keep backwards compatibility, and to keep the optimized loading,
	// the source files were flattened and included with some modifications for AMD loading.
	// A notable change is that 'jquery-ui-core' now contains 'jquery-ui-position' and 'jquery-ui-widget'.
	$scripts->add( 'jquery-ui-core', "/wp-includes/js/jquery/ui/core$suffix.js", array( 'jquery' ), '1.12.1', 1 );
	$scripts->add( 'jquery-effects-core', "/wp-includes/js/jquery/ui/effect$suffix.js", array( 'jquery' ), '1.12.1', 1 );

	$scripts->add( 'jquery-effects-blind', "/wp-includes/js/jquery/ui/effect-blind$suffix.js", array( 'jquery-effects-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-effects-bounce', "/wp-includes/js/jquery/ui/effect-bounce$suffix.js", array( 'jquery-effects-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-effects-clip', "/wp-includes/js/jquery/ui/effect-clip$suffix.js", array( 'jquery-effects-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-effects-drop', "/wp-includes/js/jquery/ui/effect-drop$suffix.js", array( 'jquery-effects-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-effects-explode', "/wp-includes/js/jquery/ui/effect-explode$suffix.js", array( 'jquery-effects-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-effects-fade', "/wp-includes/js/jquery/ui/effect-fade$suffix.js", array( 'jquery-effects-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-effects-fold', "/wp-includes/js/jquery/ui/effect-fold$suffix.js", array( 'jquery-effects-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-effects-highlight', "/wp-includes/js/jquery/ui/effect-highlight$suffix.js", array( 'jquery-effects-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-effects-puff', "/wp-includes/js/jquery/ui/effect-puff$suffix.js", array( 'jquery-effects-core', 'jquery-effects-scale' ), '1.12.1', 1 );
	$scripts->add( 'jquery-effects-pulsate', "/wp-includes/js/jquery/ui/effect-pulsate$suffix.js", array( 'jquery-effects-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-effects-scale', "/wp-includes/js/jquery/ui/effect-scale$suffix.js", array( 'jquery-effects-core', 'jquery-effects-size' ), '1.12.1', 1 );
	$scripts->add( 'jquery-effects-shake', "/wp-includes/js/jquery/ui/effect-shake$suffix.js", array( 'jquery-effects-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-effects-size', "/wp-includes/js/jquery/ui/effect-size$suffix.js", array( 'jquery-effects-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-effects-slide', "/wp-includes/js/jquery/ui/effect-slide$suffix.js", array( 'jquery-effects-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-effects-transfer', "/wp-includes/js/jquery/ui/effect-transfer$suffix.js", array( 'jquery-effects-core' ), '1.12.1', 1 );

	// Widgets
	$scripts->add( 'jquery-ui-accordion', "/wp-includes/js/jquery/ui/accordion$suffix.js", array( 'jquery-ui-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-ui-autocomplete', "/wp-includes/js/jquery/ui/autocomplete$suffix.js", array( 'jquery-ui-menu', 'wp-a11y' ), '1.12.1', 1 );
	$scripts->add( 'jquery-ui-button', "/wp-includes/js/jquery/ui/button$suffix.js", array( 'jquery-ui-core', 'jquery-ui-controlgroup', 'jquery-ui-checkboxradio' ), '1.12.1', 1 );
	$scripts->add( 'jquery-ui-datepicker', "/wp-includes/js/jquery/ui/datepicker$suffix.js", array( 'jquery-ui-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-ui-dialog', "/wp-includes/js/jquery/ui/dialog$suffix.js", array( 'jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-button' ), '1.12.1', 1 );
	$scripts->add( 'jquery-ui-menu', "/wp-includes/js/jquery/ui/menu$suffix.js", array( 'jquery-ui-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-ui-mouse', "/wp-includes/js/jquery/ui/mouse$suffix.js", array( 'jquery-ui-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-ui-progressbar', "/wp-includes/js/jquery/ui/progressbar$suffix.js", array( 'jquery-ui-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-ui-selectmenu', "/wp-includes/js/jquery/ui/selectmenu$suffix.js", array( 'jquery-ui-menu' ), '1.12.1', 1 );
	$scripts->add( 'jquery-ui-slider', "/wp-includes/js/jquery/ui/slider$suffix.js", array( 'jquery-ui-mouse' ), '1.12.1', 1 );
	$scripts->add( 'jquery-ui-spinner', "/wp-includes/js/jquery/ui/spinner$suffix.js", array( 'jquery-ui-button' ), '1.12.1', 1 );
	$scripts->add( 'jquery-ui-tabs', "/wp-includes/js/jquery/ui/tabs$suffix.js", array( 'jquery-ui-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-ui-tooltip', "/wp-includes/js/jquery/ui/tooltip$suffix.js", array( 'jquery-ui-core' ), '1.12.1', 1 );

	// New in 1.12.1
	$scripts->add( 'jquery-ui-checkboxradio', "/wp-includes/js/jquery/ui/checkboxradio$suffix.js", array( 'jquery-ui-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-ui-controlgroup', "/wp-includes/js/jquery/ui/controlgroup$suffix.js", array( 'jquery-ui-core' ), '1.12.1', 1 );

	// Interactions
	$scripts->add( 'jquery-ui-draggable', "/wp-includes/js/jquery/ui/draggable$suffix.js", array( 'jquery-ui-mouse' ), '1.12.1', 1 );
	$scripts->add( 'jquery-ui-droppable', "/wp-includes/js/jquery/ui/droppable$suffix.js", array( 'jquery-ui-draggable' ), '1.12.1', 1 );
	$scripts->add( 'jquery-ui-resizable', "/wp-includes/js/jquery/ui/resizable$suffix.js", array( 'jquery-ui-mouse' ), '1.12.1', 1 );
	$scripts->add( 'jquery-ui-selectable', "/wp-includes/js/jquery/ui/selectable$suffix.js", array( 'jquery-ui-mouse' ), '1.12.1', 1 );
	$scripts->add( 'jquery-ui-sortable', "/wp-includes/js/jquery/ui/sortable$suffix.js", array( 'jquery-ui-mouse' ), '1.12.1', 1 );

	// As of 1.12.1 `jquery-ui-position` and `jquery-ui-widget` are part of `jquery-ui-core`.
	// Listed here for back-compat.
	$scripts->add( 'jquery-ui-position', false, array( 'jquery-ui-core' ), '1.12.1', 1 );
	$scripts->add( 'jquery-ui-widget', false, array( 'jquery-ui-core' ), '1.12.1', 1 );

	// Strings for 'jquery-ui-autocomplete' live region messages.
	did_action( 'init' ) && $scripts->localize(
		'jquery-ui-autocomplete',
		'uiAutocompleteL10n',
		array(
			'noResults'    => __( 'No results found.' ),
			/* translators: Number of results found when using jQuery UI Autocomplete. */
			'oneResult'    => __( '1 result found. Use up and down arrow keys to navigate.' ),
			/* translators: %d: Number of results found when using jQuery UI Autocomplete. */
			'manyResults'  => __( '%d results found. Use up and down arrow keys to navigate.' ),
			'itemSelected' => __( 'Item selected.' ),
		)
	);

	// Deprecated, not used in core, most functionality is included in jQuery 1.3.
	$scripts->add( 'jquery-form', "/wp-includes/js/jquery/jquery.form$suffix.js", array( 'jquery' ), '4.3.0', 1 );

	// jQuery plugins.
	$scripts->add( 'jquery-color', '/wp-includes/js/jquery/jquery.color.min.js', array( 'jquery' ), '2.1.2', 1 );
	$scripts->add( 'schedule', '/wp-includes/js/jquery/jquery.schedule.js', array( 'jquery' ), '20m', 1 );
	$scripts->add( 'jquery-query', '/wp-includes/js/jquery/jquery.query.js', array( 'jquery' ), '2.2.3', 1 );
	$scripts->add( 'jquery-serialize-object', '/wp-includes/js/jquery/jquery.serialize-object.js', array( 'jquery' ), '0.2-wp', 1 );
	$scripts->add( 'jquery-hotkeys', "/wp-includes/js/jquery/jquery.hotkeys$suffix.js", array( 'jquery' ), '0.0.2m', 1 );
	$scripts->add( 'jquery-table-hotkeys', "/wp-includes/js/jquery/jquery.table-hotkeys$suffix.js", array( 'jquery', 'jquery-hotkeys' ), false, 1 );
	$scripts->add( 'jquery-touch-punch', '/wp-includes/js/jquery/jquery.ui.touch-punch.js', array( 'jquery-ui-core', 'jquery-ui-mouse' ), '0.2.2', 1 );

	// Not used any more, registered for backward compatibility.
	$scripts->add( 'suggest', "/wp-includes/js/jquery/suggest$suffix.js", array( 'jquery' ), '1.1-20110113', 1 );

	// Masonry v2 depended on jQuery. v3 does not. The older jquery-masonry handle is a shiv.
	// It sets jQuery as a dependency, as the theme may have been implicitly loading it this way.
	$scripts->add( 'imagesloaded', '/wp-includes/js/imagesloaded.min.js', array(), '4.1.4', 1 );
	$scripts->add( 'masonry', '/wp-includes/js/masonry.min.js', array( 'imagesloaded' ), '4.2.2', 1 );
	$scripts->add( 'jquery-masonry', '/wp-includes/js/jquery/jquery.masonry.min.js', array( 'jquery', 'masonry' ), '3.1.2b', 1 );

	$scripts->add( 'thickbox', '/wp-includes/js/thickbox/thickbox.js', array( 'jquery' ), '3.1-20121105', 1 );
	did_action( 'init' ) && $scripts->localize(
		'thickbox',
		'thickboxL10n',
		array(
			'next'             => __( 'Next &gt;' ),
			'prev'             => __( '&lt; Prev' ),
			'image'            => __( 'Image' ),
			'of'               => __( 'of' ),
			'close'            => __( 'Close' ),
			'noiframes'        => __( 'This feature requires inline frames. You have iframes disabled or your browser does not support them.' ),
			'loadingAnimation' => includes_url( 'js/thickbox/loadingAnimation.gif' ),
		)
	);

	$scripts->add( 'jcrop', '/wp-includes/js/jcrop/jquery.Jcrop.min.js', array( 'jquery' ), '0.9.12' );

	$scripts->add( 'swfobject', '/wp-includes/js/swfobject.js', array(), '2.2-20120417' );

	// Error messages for Plupload.
	$uploader_l10n = array(
		'queue_limit_exceeded'      => __( 'You have attempted to queue too many files.' ),
		/* translators: %s: File name. */
		'file_exceeds_size_limit'   => __( '%s exceeds the maximum upload size for this site.' ),
		'zero_byte_file'            => __( 'This file is empty. Please try another.' ),
		'invalid_filetype'          => __( 'Sorry, this file type is not permitted for security reasons.' ),
		'not_an_image'              => __( 'This file is not an image. Please try another.' ),
		'image_memory_exceeded'     => __( 'Memory exceeded. Please try another smaller file.' ),
		'image_dimensions_exceeded' => __( 'This is larger than the maximum size. Please try another.' ),
		'default_error'             => __( 'An error occurred in the upload. Please try again later.' ),
		'missing_upload_url'        => __( 'There was a configuration error. Please contact the server administrator.' ),
		'upload_limit_exceeded'     => __( 'You may only upload 1 file.' ),
		'http_error'                => __( 'Unexpected response from the server. The file may have been uploaded successfully. Check in the Media Library or reload the page.' ),
		'http_error_image'          => __( 'Post-processing of the image failed likely because the server is busy or does not have enough resources. Uploading a smaller image may help. Suggested maximum size is 2500 pixels.' ),
		'upload_failed'             => __( 'Upload failed.' ),
		/* translators: 1: Opening link tag, 2: Closing link tag. */
		'big_upload_failed'         => __( 'Please try uploading this file with the %1$sbrowser uploader%2$s.' ),
		/* translators: %s: File name. */
		'big_upload_queued'         => __( '%s exceeds the maximum upload size for the multi-file uploader when used in your browser.' ),
		'io_error'                  => __( 'IO error.' ),
		'security_error'            => __( 'Security error.' ),
		'file_cancelled'            => __( 'File canceled.' ),
		'upload_stopped'            => __( 'Upload stopped.' ),
		'dismiss'                   => __( 'Dismiss' ),
		'crunching'                 => __( 'Crunching&hellip;' ),
		'deleted'                   => __( 'moved to the Trash.' ),
		/* translators: %s: File name. */
		'error_uploading'           => __( '&#8220;%s&#8221; has failed to upload.' ),
		'unsupported_image'         => __( 'This image cannot be displayed in a web browser. For best results convert it to JPEG before uploading.' ),
		'noneditable_image'         => __( 'This image cannot be processed by the web server. Convert it to JPEG or PNG before uploading.' ),
		'file_url_copied'           => __( 'The file URL has been copied to your clipboard' ),
	);

	$scripts->add( 'moxiejs', "/wp-includes/js/plupload/moxie$suffix.js", array(), '1.3.5' );
	$scripts->add( 'plupload', "/wp-includes/js/plupload/plupload$suffix.js", array( 'moxiejs' ), '2.1.9' );
	// Back compat handles:
	foreach ( array( 'all', 'html5', 'flash', 'silverlight', 'html4' ) as $handle ) {
		$scripts->add( "plupload-$handle", false, array( 'plupload' ), '2.1.1' );
	}

	$scripts->add( 'plupload-handlers', "/wp-includes/js/plupload/handlers$suffix.js", array( 'clipboard', 'jquery', 'plupload', 'underscore', 'wp-a11y', 'wp-i18n' ) );
	did_action( 'init' ) && $scripts->localize( 'plupload-handlers', 'pluploadL10n', $uploader_l10n );

	$scripts->add( 'wp-plupload', "/wp-includes/js/plupload/wp-plupload$suffix.js", array( 'plupload', 'jquery', 'json2', 'media-models' ), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'wp-plupload', 'pluploadL10n', $uploader_l10n );

	// Keep 'swfupload' for back-compat.
	$scripts->add( 'swfupload', '/wp-includes/js/swfupload/swfupload.js', array(), '2201-20110113' );
	$scripts->add( 'swfupload-all', false, array( 'swfupload' ), '2201' );
	$scripts->add( 'swfupload-handlers', "/wp-includes/js/swfupload/handlers$suffix.js", array( 'swfupload-all', 'jquery' ), '2201-20110524' );
	did_action( 'init' ) && $scripts->localize( 'swfupload-handlers', 'swfuploadL10n', $uploader_l10n );

	$scripts->add( 'comment-reply', "/wp-includes/js/comment-reply$suffix.js", array(), false, 1 );

	$scripts->add( 'json2', "/wp-includes/js/json2$suffix.js", array(), '2015-05-03' );
	did_action( 'init' ) && $scripts->add_data( 'json2', 'conditional', 'lt IE 8' );

	$scripts->add( 'underscore', "/wp-includes/js/underscore$dev_suffix.js", array(), '1.13.1', 1 );
	$scripts->add( 'backbone', "/wp-includes/js/backbone$dev_suffix.js", array( 'underscore', 'jquery' ), '1.4.0', 1 );

	$scripts->add( 'wp-util', "/wp-includes/js/wp-util$suffix.js", array( 'underscore', 'jquery' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'wp-util',
		'_wpUtilSettings',
		array(
			'ajax' => array(
				'url' => admin_url( 'admin-ajax.php', 'relative' ),
			),
		)
	);

	$scripts->add( 'wp-backbone', "/wp-includes/js/wp-backbone$suffix.js", array( 'backbone', 'wp-util' ), false, 1 );

	$scripts->add( 'revisions', "/wp-admin/js/revisions$suffix.js", array( 'wp-backbone', 'jquery-ui-slider', 'hoverIntent' ), false, 1 );

	$scripts->add( 'imgareaselect', "/wp-includes/js/imgareaselect/jquery.imgareaselect$suffix.js", array( 'jquery' ), false, 1 );

	$scripts->add( 'mediaelement', false, array( 'jquery', 'mediaelement-core', 'mediaelement-migrate' ), '4.2.16', 1 );
	$scripts->add( 'mediaelement-core', "/wp-includes/js/mediaelement/mediaelement-and-player$suffix.js", array(), '4.2.16', 1 );
	$scripts->add( 'mediaelement-migrate', "/wp-includes/js/mediaelement/mediaelement-migrate$suffix.js", array(), false, 1 );

	did_action( 'init' ) && $scripts->add_inline_script(
		'mediaelement-core',
		sprintf(
			'var mejsL10n = %s;',
			wp_json_encode(
				array(
					'language' => strtolower( strtok( determine_locale(), '_-' ) ),
					'strings'  => array(
						'mejs.download-file'       => __( 'Download File' ),
						'mejs.install-flash'       => __( 'You are using a browser that does not have Flash player enabled or installed. Please turn on your Flash player plugin or download the latest version from https://get.adobe.com/flashplayer/' ),
						'mejs.fullscreen'          => __( 'Fullscreen' ),
						'mejs.play'                => __( 'Play' ),
						'mejs.pause'               => __( 'Pause' ),
						'mejs.time-slider'         => __( 'Time Slider' ),
						'mejs.time-help-text'      => __( 'Use Left/Right Arrow keys to advance one second, Up/Down arrows to advance ten seconds.' ),
						'mejs.live-broadcast'      => __( 'Live Broadcast' ),
						'mejs.volume-help-text'    => __( 'Use Up/Down Arrow keys to increase or decrease volume.' ),
						'mejs.unmute'              => __( 'Unmute' ),
						'mejs.mute'                => __( 'Mute' ),
						'mejs.volume-slider'       => __( 'Volume Slider' ),
						'mejs.video-player'        => __( 'Video Player' ),
						'mejs.audio-player'        => __( 'Audio Player' ),
						'mejs.captions-subtitles'  => __( 'Captions/Subtitles' ),
						'mejs.captions-chapters'   => __( 'Chapters' ),
						'mejs.none'                => __( 'None' ),
						'mejs.afrikaans'           => __( 'Afrikaans' ),
						'mejs.albanian'            => __( 'Albanian' ),
						'mejs.arabic'              => __( 'Arabic' ),
						'mejs.belarusian'          => __( 'Belarusian' ),
						'mejs.bulgarian'           => __( 'Bulgarian' ),
						'mejs.catalan'             => __( 'Catalan' ),
						'mejs.chinese'             => __( 'Chinese' ),
						'mejs.chinese-simplified'  => __( 'Chinese (Simplified)' ),
						'mejs.chinese-traditional' => __( 'Chinese (Traditional)' ),
						'mejs.croatian'            => __( 'Croatian' ),
						'mejs.czech'               => __( 'Czech' ),
						'mejs.danish'              => __( 'Danish' ),
						'mejs.dutch'               => __( 'Dutch' ),
						'mejs.english'             => __( 'English' ),
						'mejs.estonian'            => __( 'Estonian' ),
						'mejs.filipino'            => __( 'Filipino' ),
						'mejs.finnish'             => __( 'Finnish' ),
						'mejs.french'              => __( 'French' ),
						'mejs.galician'            => __( 'Galician' ),
						'mejs.german'              => __( 'German' ),
						'mejs.greek'               => __( 'Greek' ),
						'mejs.haitian-creole'      => __( 'Haitian Creole' ),
						'mejs.hebrew'              => __( 'Hebrew' ),
						'mejs.hindi'               => __( 'Hindi' ),
						'mejs.hungarian'           => __( 'Hungarian' ),
						'mejs.icelandic'           => __( 'Icelandic' ),
						'mejs.indonesian'          => __( 'Indonesian' ),
						'mejs.irish'               => __( 'Irish' ),
						'mejs.italian'             => __( 'Italian' ),
						'mejs.japanese'            => __( 'Japanese' ),
						'mejs.korean'              => __( 'Korean' ),
						'mejs.latvian'             => __( 'Latvian' ),
						'mejs.lithuanian'          => __( 'Lithuanian' ),
						'mejs.macedonian'          => __( 'Macedonian' ),
						'mejs.malay'               => __( 'Malay' ),
						'mejs.maltese'             => __( 'Maltese' ),
						'mejs.norwegian'           => __( 'Norwegian' ),
						'mejs.persian'             => __( 'Persian' ),
						'mejs.polish'              => __( 'Polish' ),
						'mejs.portuguese'          => __( 'Portuguese' ),
						'mejs.romanian'            => __( 'Romanian' ),
						'mejs.russian'             => __( 'Russian' ),
						'mejs.serbian'             => __( 'Serbian' ),
						'mejs.slovak'              => __( 'Slovak' ),
						'mejs.slovenian'           => __( 'Slovenian' ),
						'mejs.spanish'             => __( 'Spanish' ),
						'mejs.swahili'             => __( 'Swahili' ),
						'mejs.swedish'             => __( 'Swedish' ),
						'mejs.tagalog'             => __( 'Tagalog' ),
						'mejs.thai'                => __( 'Thai' ),
						'mejs.turkish'             => __( 'Turkish' ),
						'mejs.ukrainian'           => __( 'Ukrainian' ),
						'mejs.vietnamese'          => __( 'Vietnamese' ),
						'mejs.welsh'               => __( 'Welsh' ),
						'mejs.yiddish'             => __( 'Yiddish' ),
					),
				)
			)
		),
		'before'
	);

	$scripts->add( 'mediaelement-vimeo', '/wp-includes/js/mediaelement/renderers/vimeo.min.js', array( 'mediaelement' ), '4.2.16', 1 );
	$scripts->add( 'wp-mediaelement', "/wp-includes/js/mediaelement/wp-mediaelement$suffix.js", array( 'mediaelement' ), false, 1 );
	$mejs_settings = array(
		'pluginPath'  => includes_url( 'js/mediaelement/', 'relative' ),
		'classPrefix' => 'mejs-',
		'stretching'  => 'responsive',
	);
	did_action( 'init' ) && $scripts->localize(
		'mediaelement',
		'_wpmejsSettings',
		/**
		 * Filters the MediaElement configuration settings.
		 *
		 * @since 4.4.0
		 *
		 * @param array $mejs_settings MediaElement settings array.
		 */
		apply_filters( 'mejs_settings', $mejs_settings )
	);

	$scripts->add( 'wp-codemirror', '/wp-includes/js/codemirror/codemirror.min.js', array(), '5.29.1-alpha-ee20357' );
	$scripts->add( 'csslint', '/wp-includes/js/codemirror/csslint.js', array(), '1.0.5' );
	$scripts->add( 'esprima', '/wp-includes/js/codemirror/esprima.js', array(), '4.0.0' );
	$scripts->add( 'jshint', '/wp-includes/js/codemirror/fakejshint.js', array( 'esprima' ), '2.9.5' );
	$scripts->add( 'jsonlint', '/wp-includes/js/codemirror/jsonlint.js', array(), '1.6.2' );
	$scripts->add( 'htmlhint', '/wp-includes/js/codemirror/htmlhint.js', array(), '0.9.14-xwp' );
	$scripts->add( 'htmlhint-kses', '/wp-includes/js/codemirror/htmlhint-kses.js', array( 'htmlhint' ) );
	$scripts->add( 'code-editor', "/wp-admin/js/code-editor$suffix.js", array( 'jquery', 'wp-codemirror', 'underscore' ) );
	$scripts->add( 'wp-theme-plugin-editor', "/wp-admin/js/theme-plugin-editor$suffix.js", array( 'common', 'wp-util', 'wp-sanitize', 'jquery', 'jquery-ui-core', 'wp-a11y', 'underscore' ) );
	$scripts->set_translations( 'wp-theme-plugin-editor' );

	$scripts->add( 'wp-playlist', "/wp-includes/js/mediaelement/wp-playlist$suffix.js", array( 'wp-util', 'backbone', 'mediaelement' ), false, 1 );

	$scripts->add( 'zxcvbn-async', "/wp-includes/js/zxcvbn-async$suffix.js", array(), '1.0' );
	did_action( 'init' ) && $scripts->localize(
		'zxcvbn-async',
		'_zxcvbnSettings',
		array(
			'src' => empty( $guessed_url ) ? includes_url( '/js/zxcvbn.min.js' ) : $scripts->base_url . '/wp-includes/js/zxcvbn.min.js',
		)
	);

	$scripts->add( 'password-strength-meter', "/wp-admin/js/password-strength-meter$suffix.js", array( 'jquery', 'zxcvbn-async' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'password-strength-meter',
		'pwsL10n',
		array(
			'unknown'  => _x( 'Password strength unknown', 'password strength' ),
			'short'    => _x( 'Very weak', 'password strength' ),
			'bad'      => _x( 'Weak', 'password strength' ),
			'good'     => _x( 'Medium', 'password strength' ),
			'strong'   => _x( 'Strong', 'password strength' ),
			'mismatch' => _x( 'Mismatch', 'password mismatch' ),
		)
	);
	$scripts->set_translations( 'password-strength-meter' );

	$scripts->add( 'application-passwords', "/wp-admin/js/application-passwords$suffix.js", array( 'jquery', 'wp-util', 'wp-api-request', 'wp-date', 'wp-i18n', 'wp-hooks' ), false, 1 );
	$scripts->set_translations( 'application-passwords' );

	$scripts->add( 'auth-app', "/wp-admin/js/auth-app$suffix.js", array( 'jquery', 'wp-api-request', 'wp-i18n', 'wp-hooks' ), false, 1 );
	$scripts->set_translations( 'auth-app' );

	$scripts->add( 'user-profile', "/wp-admin/js/user-profile$suffix.js", array( 'jquery', 'password-strength-meter', 'wp-util' ), false, 1 );
	$scripts->set_translations( 'user-profile' );
	$user_id = isset( $_GET['user_id'] ) ? (int) $_GET['user_id'] : 0;
	did_action( 'init' ) && $scripts->localize(
		'user-profile',
		'userProfileL10n',
		array(
			'user_id' => $user_id,
			'nonce'   => ( wp_installing() && ! is_multisite() ) ? '' : wp_create_nonce( 'reset-password-for-' . $user_id ),
		)
	);

	$scripts->add( 'language-chooser', "/wp-admin/js/language-chooser$suffix.js", array( 'jquery' ), false, 1 );

	$scripts->add( 'user-suggest', "/wp-admin/js/user-suggest$suffix.js", array( 'jquery-ui-autocomplete' ), false, 1 );

	$scripts->add( 'admin-bar', "/wp-includes/js/admin-bar$suffix.js", array( 'hoverintent-js' ), false, 1 );

	$scripts->add( 'wplink', "/wp-includes/js/wplink$suffix.js", array( 'jquery', 'wp-a11y' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'wplink',
		'wpLinkL10n',
		array(
			'title'          => __( 'Insert/edit link' ),
			'update'         => __( 'Update' ),
			'save'           => __( 'Add Link' ),
			'noTitle'        => __( '(no title)' ),
			'noMatchesFound' => __( 'No results found.' ),
			'linkSelected'   => __( 'Link selected.' ),
			'linkInserted'   => __( 'Link inserted.' ),
			/* translators: Minimum input length in characters to start searching posts in the "Insert/edit link" modal. */
			'minInputLength' => (int) _x( '3', 'minimum input length for searching post links' ),
		)
	);

	$scripts->add( 'wpdialogs', "/wp-includes/js/wpdialog$suffix.js", array( 'jquery-ui-dialog' ), false, 1 );

	$scripts->add( 'word-count', "/wp-admin/js/word-count$suffix.js", array(), false, 1 );

	$scripts->add( 'media-upload', "/wp-admin/js/media-upload$suffix.js", array( 'thickbox', 'shortcode' ), false, 1 );

	$scripts->add( 'hoverIntent', "/wp-includes/js/hoverIntent$suffix.js", array( 'jquery' ), '1.10.1', 1 );

	// JS-only version of hoverintent (no dependencies).
	$scripts->add( 'hoverintent-js', '/wp-includes/js/hoverintent-js.min.js', array(), '2.2.1', 1 );

	$scripts->add( 'customize-base', "/wp-includes/js/customize-base$suffix.js", array( 'jquery', 'json2', 'underscore' ), false, 1 );
	$scripts->add( 'customize-loader', "/wp-includes/js/customize-loader$suffix.js", array( 'customize-base' ), false, 1 );
	$scripts->add( 'customize-preview', "/wp-includes/js/customize-preview$suffix.js", array( 'wp-a11y', 'customize-base' ), false, 1 );
	$scripts->add( 'customize-models', '/wp-includes/js/customize-models.js', array( 'underscore', 'backbone' ), false, 1 );
	$scripts->add( 'customize-views', '/wp-includes/js/customize-views.js', array( 'jquery', 'underscore', 'imgareaselect', 'customize-models', 'media-editor', 'media-views' ), false, 1 );
	$scripts->add( 'customize-controls', "/wp-admin/js/customize-controls$suffix.js", array( 'customize-base', 'wp-a11y', 'wp-util', 'jquery-ui-core' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'customize-controls',
		'_wpCustomizeControlsL10n',
		array(
			'activate'                => __( 'Activate &amp; Publish' ),
			'save'                    => __( 'Save &amp; Publish' ), // @todo Remove as not required.
			'publish'                 => __( 'Publish' ),
			'published'               => __( 'Published' ),
			'saveDraft'               => __( 'Save Draft' ),
			'draftSaved'              => __( 'Draft Saved' ),
			'updating'                => __( 'Updating' ),
			'schedule'                => _x( 'Schedule', 'customizer changeset action/button label' ),
			'scheduled'               => _x( 'Scheduled', 'customizer changeset status' ),
			'invalid'                 => __( 'Invalid' ),
			'saveBeforeShare'         => __( 'Please save your changes in order to share the preview.' ),
			'futureDateError'         => __( 'You must supply a future date to schedule.' ),
			'saveAlert'               => __( 'The changes you made will be lost if you navigate away from this page.' ),
			'saved'                   => __( 'Saved' ),
			'cancel'                  => __( 'Cancel' ),
			'close'                   => __( 'Close' ),
			'action'                  => __( 'Action' ),
			'discardChanges'          => __( 'Discard changes' ),
			'cheatin'                 => __( 'Something went wrong.' ),
			'notAllowedHeading'       => __( 'You need a higher level of permission.' ),
			'notAllowed'              => __( 'Sorry, you are not allowed to customize this site.' ),
			'previewIframeTitle'      => __( 'Site Preview' ),
			'loginIframeTitle'        => __( 'Session expired' ),
			'collapseSidebar'         => _x( 'Hide Controls', 'label for hide controls button without length constraints' ),
			'expandSidebar'           => _x( 'Show Controls', 'label for hide controls button without length constraints' ),
			'untitledBlogName'        => __( '(Untitled)' ),
			'unknownRequestFail'      => __( 'Looks like something&#8217;s gone wrong. Wait a couple seconds, and then try again.' ),
			'themeDownloading'        => __( 'Downloading your new theme&hellip;' ),
			'themePreviewWait'        => __( 'Setting up your live preview. This may take a bit.' ),
			'revertingChanges'        => __( 'Reverting unpublished changes&hellip;' ),
			'trashConfirm'            => __( 'Are you sure you want to discard your unpublished changes?' ),
			/* translators: %s: Display name of the user who has taken over the changeset in customizer. */
			'takenOverMessage'        => __( '%s has taken over and is currently customizing.' ),
			/* translators: %s: URL to the Customizer to load the autosaved version. */
			'autosaveNotice'          => __( 'There is a more recent autosave of your changes than the one you are previewing. <a href="%s">Restore the autosave</a>' ),
			'videoHeaderNotice'       => __( 'This theme doesn&#8217;t support video headers on this page. Navigate to the front page or another page that supports video headers.' ),
			// Used for overriding the file types allowed in Plupload.
			'allowedFiles'            => __( 'Allowed Files' ),
			'customCssError'          => array(
				/* translators: %d: Error count. */
				'singular' => _n( 'There is %d error which must be fixed before you can save.', 'There are %d errors which must be fixed before you can save.', 1 ),
				/* translators: %d: Error count. */
				'plural'   => _n( 'There is %d error which must be fixed before you can save.', 'There are %d errors which must be fixed before you can save.', 2 ),
				// @todo This is lacking, as some languages have a dedicated dual form. For proper handling of plurals in JS, see #20491.
			),
			'pageOnFrontError'        => __( 'Homepage and posts page must be different.' ),
			'saveBlockedError'        => array(
				/* translators: %s: Number of invalid settings. */
				'singular' => _n( 'Unable to save due to %s invalid setting.', 'Unable to save due to %s invalid settings.', 1 ),
				/* translators: %s: Number of invalid settings. */
				'plural'   => _n( 'Unable to save due to %s invalid setting.', 'Unable to save due to %s invalid settings.', 2 ),
				// @todo This is lacking, as some languages have a dedicated dual form. For proper handling of plurals in JS, see #20491.
			),
			'scheduleDescription'     => __( 'Schedule your customization changes to publish ("go live") at a future date.' ),
			'themePreviewUnavailable' => __( 'Sorry, you can&#8217;t preview new themes when you have changes scheduled or saved as a draft. Please publish your changes, or wait until they publish to preview new themes.' ),
			'themeInstallUnavailable' => sprintf(
				/* translators: %s: URL to Add Themes admin screen. */
				__( 'You won&#8217;t be able to install new themes from here yet since your install requires SFTP credentials. For now, please <a href="%s">add themes in the admin</a>.' ),
				esc_url( admin_url( 'theme-install.php' ) )
			),
			'publishSettings'         => __( 'Publish Settings' ),
			'invalidDate'             => __( 'Invalid date.' ),
			'invalidValue'            => __( 'Invalid value.' ),
		)
	);
	$scripts->add( 'customize-selective-refresh', "/wp-includes/js/customize-selective-refresh$suffix.js", array( 'jquery', 'wp-util', 'customize-preview' ), false, 1 );

	$scripts->add( 'customize-widgets', "/wp-admin/js/customize-widgets$suffix.js", array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-droppable', 'wp-backbone', 'customize-controls' ), false, 1 );
	$scripts->add( 'customize-preview-widgets', "/wp-includes/js/customize-preview-widgets$suffix.js", array( 'jquery', 'wp-util', 'customize-preview', 'customize-selective-refresh' ), false, 1 );

	$scripts->add( 'customize-nav-menus', "/wp-admin/js/customize-nav-menus$suffix.js", array( 'jquery', 'wp-backbone', 'customize-controls', 'accordion', 'nav-menu', 'wp-sanitize' ), false, 1 );
	$scripts->add( 'customize-preview-nav-menus', "/wp-includes/js/customize-preview-nav-menus$suffix.js", array( 'jquery', 'wp-util', 'customize-preview', 'customize-selective-refresh' ), false, 1 );

	$scripts->add( 'wp-custom-header', "/wp-includes/js/wp-custom-header$suffix.js", array( 'wp-a11y' ), false, 1 );

	$scripts->add( 'accordion', "/wp-admin/js/accordion$suffix.js", array( 'jquery' ), false, 1 );

	$scripts->add( 'shortcode', "/wp-includes/js/shortcode$suffix.js", array( 'underscore' ), false, 1 );
	$scripts->add( 'media-models', "/wp-includes/js/media-models$suffix.js", array( 'wp-backbone' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'media-models',
		'_wpMediaModelsL10n',
		array(
			'settings' => array(
				'ajaxurl' => admin_url( 'admin-ajax.php', 'relative' ),
				'post'    => array( 'id' => 0 ),
			),
		)
	);

	$scripts->add( 'wp-embed', "/wp-includes/js/wp-embed$suffix.js" );

	// To enqueue media-views or media-editor, call wp_enqueue_media().
	// Both rely on numerous settings, styles, and templates to operate correctly.
	$scripts->add( 'media-views', "/wp-includes/js/media-views$suffix.js", array( 'utils', 'media-models', 'wp-plupload', 'jquery-ui-sortable', 'wp-mediaelement', 'wp-api-request', 'wp-a11y', 'clipboard' ), false, 1 );
	$scripts->set_translations( 'media-views' );

	$scripts->add( 'media-editor', "/wp-includes/js/media-editor$suffix.js", array( 'shortcode', 'media-views' ), false, 1 );
	$scripts->set_translations( 'media-editor' );
	$scripts->add( 'media-audiovideo', "/wp-includes/js/media-audiovideo$suffix.js", array( 'media-editor' ), false, 1 );
	$scripts->add( 'mce-view', "/wp-includes/js/mce-view$suffix.js", array( 'shortcode', 'jquery', 'media-views', 'media-audiovideo' ), false, 1 );

	$scripts->add( 'wp-api', "/wp-includes/js/wp-api$suffix.js", array( 'jquery', 'backbone', 'underscore', 'wp-api-request' ), false, 1 );

	if ( is_admin() ) {
		$scripts->add( 'admin-tags', "/wp-admin/js/tags$suffix.js", array( 'jquery', 'wp-ajax-response' ), false, 1 );
		$scripts->set_translations( 'admin-tags' );

		$scripts->add( 'admin-comments', "/wp-admin/js/edit-comments$suffix.js", array( 'wp-lists', 'quicktags', 'jquery-query' ), false, 1 );
		$scripts->set_translations( 'admin-comments' );
		did_action( 'init' ) && $scripts->localize(
			'admin-comments',
			'adminCommentsSettings',
			array(
				'hotkeys_highlight_first' => isset( $_GET['hotkeys_highlight_first'] ),
				'hotkeys_highlight_last'  => isset( $_GET['hotkeys_highlight_last'] ),
			)
		);

		$scripts->add( 'xfn', "/wp-admin/js/xfn$suffix.js", array( 'jquery' ), false, 1 );

		$scripts->add( 'postbox', "/wp-admin/js/postbox$suffix.js", array( 'jquery-ui-sortable', 'wp-a11y' ), false, 1 );
		$scripts->set_translations( 'postbox' );

		$scripts->add( 'tags-box', "/wp-admin/js/tags-box$suffix.js", array( 'jquery', 'tags-suggest' ), false, 1 );
		$scripts->set_translations( 'tags-box' );

		$scripts->add( 'tags-suggest', "/wp-admin/js/tags-suggest$suffix.js", array( 'jquery-ui-autocomplete', 'wp-a11y' ), false, 1 );
		$scripts->set_translations( 'tags-suggest' );

		$scripts->add( 'post', "/wp-admin/js/post$suffix.js", array( 'suggest', 'wp-lists', 'postbox', 'tags-box', 'underscore', 'word-count', 'wp-a11y', 'wp-sanitize', 'clipboard' ), false, 1 );
		$scripts->set_translations( 'post' );

		$scripts->add( 'editor-expand', "/wp-admin/js/editor-expand$suffix.js", array( 'jquery', 'underscore' ), false, 1 );

		$scripts->add( 'link', "/wp-admin/js/link$suffix.js", array( 'wp-lists', 'postbox' ), false, 1 );

		$scripts->add( 'comment', "/wp-admin/js/comment$suffix.js", array( 'jquery', 'postbox' ), false, 1 );
		$scripts->set_translations( 'comment' );

		$scripts->add( 'admin-gallery', "/wp-admin/js/gallery$suffix.js", array( 'jquery-ui-sortable' ) );

		$scripts->add( 'admin-widgets', "/wp-admin/js/widgets$suffix.js", array( 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'wp-a11y' ), false, 1 );
		$scripts->set_translations( 'admin-widgets' );

		$scripts->add( 'media-widgets', "/wp-admin/js/widgets/media-widgets$suffix.js", array( 'jquery', 'media-models', 'media-views', 'wp-api-request' ) );
		$scripts->add_inline_script( 'media-widgets', 'wp.mediaWidgets.init();', 'after' );

		$scripts->add( 'media-audio-widget', "/wp-admin/js/widgets/media-audio-widget$suffix.js", array( 'media-widgets', 'media-audiovideo' ) );
		$scripts->add( 'media-image-widget', "/wp-admin/js/widgets/media-image-widget$suffix.js", array( 'media-widgets' ) );
		$scripts->add( 'media-gallery-widget', "/wp-admin/js/widgets/media-gallery-widget$suffix.js", array( 'media-widgets' ) );
		$scripts->add( 'media-video-widget', "/wp-admin/js/widgets/media-video-widget$suffix.js", array( 'media-widgets', 'media-audiovideo', 'wp-api-request' ) );
		$scripts->add( 'text-widgets', "/wp-admin/js/widgets/text-widgets$suffix.js", array( 'jquery', 'backbone', 'editor', 'wp-util', 'wp-a11y' ) );
		$scripts->add( 'custom-html-widgets', "/wp-admin/js/widgets/custom-html-widgets$suffix.js", array( 'jquery', 'backbone', 'wp-util', 'jquery-ui-core', 'wp-a11y' ) );

		$scripts->add( 'theme', "/wp-admin/js/theme$suffix.js", array( 'wp-backbone', 'wp-a11y', 'customize-base' ), false, 1 );

		$scripts->add( 'inline-edit-post', "/wp-admin/js/inline-edit-post$suffix.js", array( 'jquery', 'tags-suggest', 'wp-a11y' ), false, 1 );
		$scripts->set_translations( 'inline-edit-post' );

		$scripts->add( 'inline-edit-tax', "/wp-admin/js/inline-edit-tax$suffix.js", array( 'jquery', 'wp-a11y' ), false, 1 );
		$scripts->set_translations( 'inline-edit-tax' );

		$scripts->add( 'plugin-install', "/wp-admin/js/plugin-install$suffix.js", array( 'jquery', 'jquery-ui-core', 'thickbox' ), false, 1 );
		$scripts->set_translations( 'plugin-install' );

		$scripts->add( 'site-health', "/wp-admin/js/site-health$suffix.js", array( 'clipboard', 'jquery', 'wp-util', 'wp-a11y', 'wp-api-request', 'wp-url', 'wp-i18n', 'wp-hooks' ), false, 1 );
		$scripts->set_translations( 'site-health' );

		$scripts->add( 'privacy-tools', "/wp-admin/js/privacy-tools$suffix.js", array( 'jquery', 'wp-a11y' ), false, 1 );
		$scripts->set_translations( 'privacy-tools' );

		$scripts->add( 'updates', "/wp-admin/js/updates$suffix.js", array( 'common', 'jquery', 'wp-util', 'wp-a11y', 'wp-sanitize' ), false, 1 );
		$scripts->set_translations( 'updates' );
		did_action( 'init' ) && $scripts->localize(
			'updates',
			'_wpUpdatesSettings',
			array(
				'ajax_nonce' => wp_create_nonce( 'updates' ),
			)
		);

		$scripts->add( 'farbtastic', '/wp-admin/js/farbtastic.js', array( 'jquery' ), '1.2' );

		$scripts->add( 'iris', '/wp-admin/js/iris.min.js', array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), '1.0.7', 1 );
		$scripts->add( 'wp-color-picker', "/wp-admin/js/color-picker$suffix.js", array( 'iris' ), false, 1 );
		$scripts->set_translations( 'wp-color-picker' );

		$scripts->add( 'dashboard', "/wp-admin/js/dashboard$suffix.js", array( 'jquery', 'admin-comments', 'postbox', 'wp-util', 'wp-a11y', 'wp-date' ), false, 1 );
		$scripts->set_translations( 'dashboard' );

		$scripts->add( 'list-revisions', "/wp-includes/js/wp-list-revisions$suffix.js" );

		$scripts->add( 'media-grid', "/wp-includes/js/media-grid$suffix.js", array( 'media-editor' ), false, 1 );
		$scripts->add( 'media', "/wp-admin/js/media$suffix.js", array( 'jquery' ), false, 1 );
		$scripts->set_translations( 'media' );

		$scripts->add( 'image-edit', "/wp-admin/js/image-edit$suffix.js", array( 'jquery', 'jquery-ui-core', 'json2', 'imgareaselect', 'wp-a11y' ), false, 1 );
		$scripts->set_translations( 'image-edit' );

		$scripts->add( 'set-post-thumbnail', "/wp-admin/js/set-post-thumbnail$suffix.js", array( 'jquery' ), false, 1 );
		$scripts->set_translations( 'set-post-thumbnail' );

		/*
		 * Navigation Menus: Adding underscore as a dependency to utilize _.debounce
		 * see https://core.trac.wordpress.org/ticket/42321
		 */
		$scripts->add( 'nav-menu', "/wp-admin/js/nav-menu$suffix.js", array( 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'wp-lists', 'postbox', 'json2', 'underscore' ) );
		$scripts->set_translations( 'nav-menu' );

		$scripts->add( 'custom-header', '/wp-admin/js/custom-header.js', array( 'jquery-masonry' ), false, 1 );
		$scripts->add( 'custom-background', "/wp-admin/js/custom-background$suffix.js", array( 'wp-color-picker', 'media-views' ), false, 1 );
		$scripts->add( 'media-gallery', "/wp-admin/js/media-gallery$suffix.js", array( 'jquery' ), false, 1 );

		$scripts->add( 'svg-painter', '/wp-admin/js/svg-painter.js', array( 'jquery' ), false, 1 );
	}
}

/**
 * Assign default styles to $styles object.
 *
 * Nothing is returned, because the $styles parameter is passed by reference.
 * Meaning that whatever object is passed will be updated without having to
 * reassign the variable that was passed back to the same value. This saves
 * memory.
 *
 * Adding default styles is not the only task, it also assigns the base_url
 * property, the default version, and text direction for the object.
 *
 * @since 2.6.0
 *
 * @param WP_Styles $styles
 */
function wp_default_styles( $styles ) {
	// Include an unmodified $wp_version.
	require ABSPATH . WPINC . '/version.php';

	if ( ! defined( 'SCRIPT_DEBUG' ) ) {
		define( 'SCRIPT_DEBUG', false !== strpos( $wp_version, '-src' ) );
	}

	$guessurl = site_url();

	if ( ! $guessurl ) {
		$guessurl = wp_guess_url();
	}

	$styles->base_url        = $guessurl;
	$styles->content_url     = defined( 'WP_CONTENT_URL' ) ? WP_CONTENT_URL : '';
	$styles->default_version = get_bloginfo( 'version' );
	$styles->text_direction  = function_exists( 'is_rtl' ) && is_rtl() ? 'rtl' : 'ltr';
	$styles->default_dirs    = array( '/wp-admin/', '/wp-includes/css/' );

	// Open Sans is no longer used by core, but may be relied upon by themes and plugins.
	$open_sans_font_url = '';

	/*
	 * translators: If there are characters in your language that are not supported
	 * by Open Sans, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Open Sans font: on or off' ) ) {
		$subsets = 'latin,latin-ext';

		/*
		 * translators: To add an additional Open Sans character subset specific to your language,
		 * translate this to 'greek', 'cyrillic' or 'vietnamese'. Do not translate into your own language.
		 */
		$subset = _x( 'no-subset', 'Open Sans font: add new subset (greek, cyrillic, vietnamese)' );

		if ( 'cyrillic' === $subset ) {
			$subsets .= ',cyrillic,cyrillic-ext';
		} elseif ( 'greek' === $subset ) {
			$subsets .= ',greek,greek-ext';
		} elseif ( 'vietnamese' === $subset ) {
			$subsets .= ',vietnamese';
		}

		// Hotlink Open Sans, for now.
		$open_sans_font_url = "https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,300,400,600&subset=$subsets&display=fallback";
	}

	// Register a stylesheet for the selected admin color scheme.
	$styles->add( 'colors', true, array( 'wp-admin', 'buttons' ) );

	$suffix = SCRIPT_DEBUG ? '' : '.min';

	// Admin CSS.
	$styles->add( 'common', "/wp-admin/css/common$suffix.css" );
	$styles->add( 'forms', "/wp-admin/css/forms$suffix.css" );
	$styles->add( 'admin-menu', "/wp-admin/css/admin-menu$suffix.css" );
	$styles->add( 'dashboard', "/wp-admin/css/dashboard$suffix.css" );
	$styles->add( 'list-tables', "/wp-admin/css/list-tables$suffix.css" );
	$styles->add( 'edit', "/wp-admin/css/edit$suffix.css" );
	$styles->add( 'revisions', "/wp-admin/css/revisions$suffix.css" );
	$styles->add( 'media', "/wp-admin/css/media$suffix.css" );
	$styles->add( 'themes', "/wp-admin/css/themes$suffix.css" );
	$styles->add( 'about', "/wp-admin/css/about$suffix.css" );
	$styles->add( 'nav-menus', "/wp-admin/css/nav-menus$suffix.css" );
	$styles->add( 'widgets', "/wp-admin/css/widgets$suffix.css", array( 'wp-pointer' ) );
	$styles->add( 'site-icon', "/wp-admin/css/site-icon$suffix.css" );
	$styles->add( 'l10n', "/wp-admin/css/l10n$suffix.css" );
	$styles->add( 'code-editor', "/wp-admin/css/code-editor$suffix.css", array( 'wp-codemirror' ) );
	$styles->add( 'site-health', "/wp-admin/css/site-health$suffix.css" );

	$styles->add( 'wp-admin', false, array( 'dashicons', 'common', 'forms', 'admin-menu', 'dashboard', 'list-tables', 'edit', 'revisions', 'media', 'themes', 'about', 'nav-menus', 'widgets', 'site-icon', 'l10n' ) );

	$styles->add( 'login', "/wp-admin/css/login$suffix.css", array( 'dashicons', 'buttons', 'forms', 'l10n' ) );
	$styles->add( 'install', "/wp-admin/css/install$suffix.css", array( 'dashicons', 'buttons', 'forms', 'l10n' ) );
	$styles->add( 'wp-color-picker', "/wp-admin/css/color-picker$suffix.css" );
	$styles->add( 'customize-controls', "/wp-admin/css/customize-controls$suffix.css", array( 'wp-admin', 'colors', 'imgareaselect' ) );
	$styles->add( 'customize-widgets', "/wp-admin/css/customize-widgets$suffix.css", array( 'wp-admin', 'colors' ) );
	$styles->add( 'customize-nav-menus', "/wp-admin/css/customize-nav-menus$suffix.css", array( 'wp-admin', 'colors' ) );

	// Common dependencies.
	$styles->add( 'buttons', "/wp-includes/css/buttons$suffix.css" );
	$styles->add( 'dashicons', "/wp-includes/css/dashicons$suffix.css" );

	// Includes CSS.
	$styles->add( 'admin-bar', "/wp-includes/css/admin-bar$suffix.css", array( 'dashicons' ) );
	$styles->add( 'wp-auth-check', "/wp-includes/css/wp-auth-check$suffix.css", array( 'dashicons' ) );
	$styles->add( 'editor-buttons', "/wp-includes/css/editor$suffix.css", array( 'dashicons' ) );
	$styles->add( 'media-views', "/wp-includes/css/media-views$suffix.css", array( 'buttons', 'dashicons', 'wp-mediaelement' ) );
	$styles->add( 'wp-pointer', "/wp-includes/css/wp-pointer$suffix.css", array( 'dashicons' ) );
	$styles->add( 'customize-preview', "/wp-includes/css/customize-preview$suffix.css", array( 'dashicons' ) );
	$styles->add( 'wp-embed-template-ie', "/wp-includes/css/wp-embed-template-ie$suffix.css" );
	$styles->add_data( 'wp-embed-template-ie', 'conditional', 'lte IE 8' );

	// External libraries and friends.
	$styles->add( 'imgareaselect', '/wp-includes/js/imgareaselect/imgareaselect.css', array(), '0.9.8' );
	$styles->add( 'wp-jquery-ui-dialog', "/wp-includes/css/jquery-ui-dialog$suffix.css", array( 'dashicons' ) );
	$styles->add( 'mediaelement', '/wp-includes/js/mediaelement/mediaelementplayer-legacy.min.css', array(), '4.2.16' );
	$styles->add( 'wp-mediaelement', "/wp-includes/js/mediaelement/wp-mediaelement$suffix.css", array( 'mediaelement' ) );
	$styles->add( 'thickbox', '/wp-includes/js/thickbox/thickbox.css', array( 'dashicons' ) );
	$styles->add( 'wp-codemirror', '/wp-includes/js/codemirror/codemirror.min.css', array(), '5.29.1-alpha-ee20357' );

	// Deprecated CSS.
	$styles->add( 'deprecated-media', "/wp-admin/css/deprecated-media$suffix.css" );
	$styles->add( 'farbtastic', "/wp-admin/css/farbtastic$suffix.css", array(), '1.3u1' );
	$styles->add( 'jcrop', '/wp-includes/js/jcrop/jquery.Jcrop.min.css', array(), '0.9.12' );
	$styles->add( 'colors-fresh', false, array( 'wp-admin', 'buttons' ) ); // Old handle.
	$styles->add( 'open-sans', $open_sans_font_url ); // No longer used in core as of 4.6.

	// Noto Serif is no longer used by core, but may be relied upon by themes and plugins.
	$fonts_url = '';

	/*
	 * translators: Use this to specify the proper Google Font name and variants
	 * to load that is supported by your language. Do not translate.
	 * Set to 'off' to disable loading.
	 */
	$font_family = _x( 'Noto Serif:400,400i,700,700i', 'Google Font Name and Variants' );
	if ( 'off' !== $font_family ) {
		$fonts_url = 'https://fonts.googleapis.com/css?family=' . urlencode( $font_family );
	}
	$styles->add( 'wp-editor-font', $fonts_url ); // No longer used in core as of 5.7.
	$block_library_theme_path = WPINC . "/css/dist/block-library/theme$suffix.css";
	$styles->add( 'wp-block-library-theme', "/$block_library_theme_path" );
	$styles->add_data( 'wp-block-library-theme', 'path', ABSPATH . $block_library_theme_path );

	$styles->add(
		'wp-reset-editor-styles',
		"/wp-includes/css/dist/block-library/reset$suffix.css",
		array( 'common', 'forms' ) // Make sure the reset is loaded after the default WP Admin styles.
	);

	$styles->add(
		'wp-editor-classic-layout-styles',
		"/wp-includes/css/dist/edit-post/classic$suffix.css",
		array()
	);

	$wp_edit_blocks_dependencies = array(
		'wp-components',
		'wp-editor',
		// This need to be added before the block library styles,
		// The block library styles override the "reset" styles.
		'wp-reset-editor-styles',
		'wp-block-library',
		'wp-reusable-blocks',
	);

	// Only load the default layout and margin styles for themes without theme.json file.
	if ( ! WP_Theme_JSON_Resolver::theme_has_support() ) {
		$wp_edit_blocks_dependencies[] = 'wp-editor-classic-layout-styles';
	}

	global $editor_styles;
	if ( ! is_array( $editor_styles ) || count( $editor_styles ) === 0 ) {
		// Include opinionated block styles if no $editor_styles are declared, so the editor never appears broken.
		$wp_edit_blocks_dependencies[] = 'wp-block-library-theme';
	}

	$styles->add(
		'wp-edit-blocks',
		"/wp-includes/css/dist/block-library/editor$suffix.css",
		$wp_edit_blocks_dependencies
	);

	$package_styles = array(
		'block-editor'         => array( 'wp-components' ),
		'block-library'        => array(),
		'block-directory'      => array(),
		'components'           => array(),
		'edit-post'            => array(
			'wp-components',
			'wp-block-editor',
			'wp-editor',
			'wp-edit-blocks',
			'wp-block-library',
			'wp-nux',
		),
		'editor'               => array(
			'wp-components',
			'wp-block-editor',
			'wp-nux',
			'wp-reusable-blocks',
		),
		'format-library'       => array(),
		'list-reusable-blocks' => array( 'wp-components' ),
		'reusable-blocks'      => array( 'wp-components' ),
		'nux'                  => array( 'wp-components' ),
		'widgets'              => array(
			'wp-components',
		),
		'edit-widgets'         => array(
			'wp-widgets',
			'wp-block-editor',
			'wp-edit-blocks',
			'wp-block-library',
			'wp-reusable-blocks',
		),
		'customize-widgets'    => array(
			'wp-widgets',
			'wp-block-editor',
			'wp-edit-blocks',
			'wp-block-library',
			'wp-reusable-blocks',
		),
	);

	foreach ( $package_styles as $package => $dependencies ) {
		$handle = 'wp-' . $package;
		$path   = "/wp-includes/css/dist/$package/style$suffix.css";

		if ( 'block-library' === $package && wp_should_load_separate_core_block_assets() ) {
			$path = "/wp-includes/css/dist/$package/common$suffix.css";
		}
		$styles->add( $handle, $path, $dependencies );
		$styles->add_data( $handle, 'path', ABSPATH . $path );
	}

	// RTL CSS.
	$rtl_styles = array(
		// Admin CSS.
		'common',
		'forms',
		'admin-menu',
		'dashboard',
		'list-tables',
		'edit',
		'revisions',
		'media',
		'themes',
		'about',
		'nav-menus',
		'widgets',
		'site-icon',
		'l10n',
		'install',
		'wp-color-picker',
		'customize-controls',
		'customize-widgets',
		'customize-nav-menus',
		'customize-preview',
		'login',
		'site-health',
		// Includes CSS.
		'buttons',
		'admin-bar',
		'wp-auth-check',
		'editor-buttons',
		'media-views',
		'wp-pointer',
		'wp-jquery-ui-dialog',
		// Package styles.
		'wp-reset-editor-styles',
		'wp-editor-classic-layout-styles',
		'wp-block-library-theme',
		'wp-edit-blocks',
		'wp-block-editor',
		'wp-block-library',
		'wp-block-directory',
		'wp-components',
		'wp-customize-widgets',
		'wp-edit-post',
		'wp-edit-widgets',
		'wp-editor',
		'wp-format-library',
		'wp-list-reusable-blocks',
		'wp-reusable-blocks',
		'wp-nux',
		'wp-widgets',
		// Deprecated CSS.
		'deprecated-media',
		'farbtastic',
	);

	foreach ( $rtl_styles as $rtl_style ) {
		$styles->add_data( $rtl_style, 'rtl', 'replace' );
		if ( $suffix ) {
			$styles->add_data( $rtl_style, 'suffix', $suffix );
		}
	}
}

/**
 * Reorder JavaScript scripts array to place prototype before jQuery.
 *
 * @since 2.3.1
 *
 * @param array $js_array JavaScript scripts array
 * @return array Reordered array, if needed.
 */
function wp_prototype_before_jquery( $js_array ) {
	$prototype = array_search( 'prototype', $js_array, true );

	if ( false === $prototype ) {
		return $js_array;
	}

	$jquery = array_search( 'jquery', $js_array, true );

	if ( false === $jquery ) {
		return $js_array;
	}

	if ( $prototype < $jquery ) {
		return $js_array;
	}

	unset( $js_array[ $prototype ] );

	array_splice( $js_array, $jquery, 0, 'prototype' );

	return $js_array;
}

/**
 * Load localized data on print rather than initialization.
 *
 * These localizations require information that may not be loaded even by init.
 *
 * @since 2.5.0
 */
function wp_just_in_time_script_localization() {

	wp_localize_script(
		'autosave',
		'autosaveL10n',
		array(
			'autosaveInterval' => AUTOSAVE_INTERVAL,
			'blog_id'          => get_current_blog_id(),
		)
	);

	wp_localize_script(
		'mce-view',
		'mceViewL10n',
		array(
			'shortcodes' => ! empty( $GLOBALS['shortcode_tags'] ) ? array_keys( $GLOBALS['shortcode_tags'] ) : array(),
		)
	);

	wp_localize_script(
		'word-count',
		'wordCountL10n',
		array(
			/*
			 * translators: If your word count is based on single characters (e.g. East Asian characters),
			 * enter 'characters_excluding_spaces' or 'characters_including_spaces'. Otherwise, enter 'words'.
			 * Do not translate into your own language.
			 */
			'type'       => _x( 'words', 'Word count type. Do not translate!' ),
			'shortcodes' => ! empty( $GLOBALS['shortcode_tags'] ) ? array_keys( $GLOBALS['shortcode_tags'] ) : array(),
		)
	);
}

/**
 * Localizes the jQuery UI datepicker.
 *
 * @since 4.6.0
 *
 * @link https://api.jqueryui.com/datepicker/#options
 *
 * @global WP_Locale $wp_locale WordPress date and time locale object.
 */
function wp_localize_jquery_ui_datepicker() {
	global $wp_locale;

	if ( ! wp_script_is( 'jquery-ui-datepicker', 'enqueued' ) ) {
		return;
	}

	// Convert the PHP date format into jQuery UI's format.
	$datepicker_date_format = str_replace(
		array(
			'd',
			'j',
			'l',
			'z', // Day.
			'F',
			'M',
			'n',
			'm', // Month.
			'Y',
			'y', // Year.
		),
		array(
			'dd',
			'd',
			'DD',
			'o',
			'MM',
			'M',
			'm',
			'mm',
			'yy',
			'y',
		),
		get_option( 'date_format' )
	);

	$datepicker_defaults = wp_json_encode(
		array(
			'closeText'       => __( 'Close' ),
			'currentText'     => __( 'Today' ),
			'monthNames'      => array_values( $wp_locale->month ),
			'monthNamesShort' => array_values( $wp_locale->month_abbrev ),
			'nextText'        => __( 'Next' ),
			'prevText'        => __( 'Previous' ),
			'dayNames'        => array_values( $wp_locale->weekday ),
			'dayNamesShort'   => array_values( $wp_locale->weekday_abbrev ),
			'dayNamesMin'     => array_values( $wp_locale->weekday_initial ),
			'dateFormat'      => $datepicker_date_format,
			'firstDay'        => absint( get_option( 'start_of_week' ) ),
			'isRTL'           => $wp_locale->is_rtl(),
		)
	);

	wp_add_inline_script( 'jquery-ui-datepicker', "jQuery(document).ready(function(jQuery){jQuery.datepicker.setDefaults({$datepicker_defaults});});" );
}

/**
 * Localizes community events data that needs to be passed to dashboard.js.
 *
 * @since 4.8.0
 */
function wp_localize_community_events() {
	if ( ! wp_script_is( 'dashboard' ) ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/class-wp-community-events.php';

	$user_id            = get_current_user_id();
	$saved_location     = get_user_option( 'community-events-location', $user_id );
	$saved_ip_address   = isset( $saved_location['ip'] ) ? $saved_location['ip'] : false;
	$current_ip_address = WP_Community_Events::get_unsafe_client_ip();

	/*
	 * If the user's location is based on their IP address, then update their
	 * location when their IP address changes. This allows them to see events
	 * in their current city when travelling. Otherwise, they would always be
	 * shown events in the city where they were when they first loaded the
	 * Dashboard, which could have been months or years ago.
	 */
	if ( $saved_ip_address && $current_ip_address && $current_ip_address !== $saved_ip_address ) {
		$saved_location['ip'] = $current_ip_address;
		update_user_meta( $user_id, 'community-events-location', $saved_location );
	}

	$events_client = new WP_Community_Events( $user_id, $saved_location );

	wp_localize_script(
		'dashboard',
		'communityEventsData',
		array(
			'nonce'       => wp_create_nonce( 'community_events' ),
			'cache'       => $events_client->get_cached_events(),
			'time_format' => get_option( 'time_format' ),
		)
	);
}

/**
 * Administration Screen CSS for changing the styles.
 *
 * If installing the 'wp-admin/' directory will be replaced with './'.
 *
 * The $_wp_admin_css_colors global manages the Administration Screens CSS
 * stylesheet that is loaded. The option that is set is 'admin_color' and is the
 * color and key for the array. The value for the color key is an object with
 * a 'url' parameter that has the URL path to the CSS file.
 *
 * The query from $src parameter will be appended to the URL that is given from
 * the $_wp_admin_css_colors array value URL.
 *
 * @since 2.6.0
 *
 * @global array $_wp_admin_css_colors
 *
 * @param string $src    Source URL.
 * @param string $handle Either 'colors' or 'colors-rtl'.
 * @return string|false URL path to CSS stylesheet for Administration Screens.
 */
function wp_style_loader_src( $src, $handle ) {
	global $_wp_admin_css_colors;

	if ( wp_installing() ) {
		return preg_replace( '#^wp-admin/#', './', $src );
	}

	if ( 'colors' === $handle ) {
		$color = get_user_option( 'admin_color' );

		if ( empty( $color ) || ! isset( $_wp_admin_css_colors[ $color ] ) ) {
			$color = 'fresh';
		}

		$color = $_wp_admin_css_colors[ $color ];
		$url   = $color->url;

		if ( ! $url ) {
			return false;
		}

		$parsed = parse_url( $src );
		if ( isset( $parsed['query'] ) && $parsed['query'] ) {
			wp_parse_str( $parsed['query'], $qv );
			$url = add_query_arg( $qv, $url );
		}

		return $url;
	}

	return $src;
}

/**
 * Prints the script queue in the HTML head on admin pages.
 *
 * Postpones the scripts that were queued for the footer.
 * print_footer_scripts() is called in the footer to print these scripts.
 *
 * @since 2.8.0
 *
 * @see wp_print_scripts()
 *
 * @global bool $concatenate_scripts
 *
 * @return array
 */
function print_head_scripts() {
	global $concatenate_scripts;

	if ( ! did_action( 'wp_print_scripts' ) ) {
		/** This action is documented in wp-includes/functions.wp-scripts.php */
		do_action( 'wp_print_scripts' );
	}

	$wp_scripts = wp_scripts();

	script_concat_settings();
	$wp_scripts->do_concat = $concatenate_scripts;
	$wp_scripts->do_head_items();

	/**
	 * Filters whether to print the head scripts.
	 *
	 * @since 2.8.0
	 *
	 * @param bool $print Whether to print the head scripts. Default true.
	 */
	if ( apply_filters( 'print_head_scripts', true ) ) {
		_print_scripts();
	}

	$wp_scripts->reset();
	return $wp_scripts->done;
}

/**
 * Prints the scripts that were queued for the footer or too late for the HTML head.
 *
 * @since 2.8.0
 *
 * @global WP_Scripts $wp_scripts
 * @global bool       $concatenate_scripts
 *
 * @return array
 */
function print_footer_scripts() {
	global $wp_scripts, $concatenate_scripts;

	if ( ! ( $wp_scripts instanceof WP_Scripts ) ) {
		return array(); // No need to run if not instantiated.
	}
	script_concat_settings();
	$wp_scripts->do_concat = $concatenate_scripts;
	$wp_scripts->do_footer_items();

	/**
	 * Filters whether to print the footer scripts.
	 *
	 * @since 2.8.0
	 *
	 * @param bool $print Whether to print the footer scripts. Default true.
	 */
	if ( apply_filters( 'print_footer_scripts', true ) ) {
		_print_scripts();
	}

	$wp_scripts->reset();
	return $wp_scripts->done;
}

/**
 * Print scripts (internal use only)
 *
 * @ignore
 *
 * @global WP_Scripts $wp_scripts
 * @global bool       $compress_scripts
 */
function _print_scripts() {
	global $wp_scripts, $compress_scripts;

	$zip = $compress_scripts ? 1 : 0;
	if ( $zip && defined( 'ENFORCE_GZIP' ) && ENFORCE_GZIP ) {
		$zip = 'gzip';
	}

	$concat    = trim( $wp_scripts->concat, ', ' );
	$type_attr = current_theme_supports( 'html5', 'script' ) ? '' : " type='text/javascript'";

	if ( $concat ) {
		if ( ! empty( $wp_scripts->print_code ) ) {
			echo "\n<script{$type_attr}>\n";
			echo "/* <![CDATA[ */\n"; // Not needed in HTML 5.
			echo $wp_scripts->print_code;
			echo "/* ]]> */\n";
			echo "</script>\n";
		}

		$concat       = str_split( $concat, 128 );
		$concatenated = '';

		foreach ( $concat as $key => $chunk ) {
			$concatenated .= "&load%5Bchunk_{$key}%5D={$chunk}";
		}

		$src = $wp_scripts->base_url . "/wp-admin/load-scripts.php?c={$zip}" . $concatenated . '&ver=' . $wp_scripts->default_version;
		echo "<script{$type_attr} src='" . esc_attr( $src ) . "'></script>\n";
	}

	if ( ! empty( $wp_scripts->print_html ) ) {
		echo $wp_scripts->print_html;
	}
}

/**
 * Prints the script queue in the HTML head on the front end.
 *
 * Postpones the scripts that were queued for the footer.
 * wp_print_footer_scripts() is called in the footer to print these scripts.
 *
 * @since 2.8.0
 *
 * @global WP_Scripts $wp_scripts
 *
 * @return array
 */
function wp_print_head_scripts() {
	if ( ! did_action( 'wp_print_scripts' ) ) {
		/** This action is documented in wp-includes/functions.wp-scripts.php */
		do_action( 'wp_print_scripts' );
	}

	global $wp_scripts;

	if ( ! ( $wp_scripts instanceof WP_Scripts ) ) {
		return array(); // No need to run if nothing is queued.
	}
	return print_head_scripts();
}

/**
 * Private, for use in *_footer_scripts hooks
 *
 * @since 3.3.0
 */
function _wp_footer_scripts() {
	print_late_styles();
	print_footer_scripts();
}

/**
 * Hooks to print the scripts and styles in the footer.
 *
 * @since 2.8.0
 */
function wp_print_footer_scripts() {
	/**
	 * Fires when footer scripts are printed.
	 *
	 * @since 2.8.0
	 */
	do_action( 'wp_print_footer_scripts' );
}

/**
 * Wrapper for do_action( 'wp_enqueue_scripts' ).
 *
 * Allows plugins to queue scripts for the front end using wp_enqueue_script().
 * Runs first in wp_head() where all is_home(), is_page(), etc. functions are available.
 *
 * @since 2.8.0
 */
function wp_enqueue_scripts() {
	/**
	 * Fires when scripts and styles are enqueued.
	 *
	 * @since 2.8.0
	 */
	do_action( 'wp_enqueue_scripts' );
}

/**
 * Prints the styles queue in the HTML head on admin pages.
 *
 * @since 2.8.0
 *
 * @global bool $concatenate_scripts
 *
 * @return array
 */
function print_admin_styles() {
	global $concatenate_scripts;

	$wp_styles = wp_styles();

	script_concat_settings();
	$wp_styles->do_concat = $concatenate_scripts;
	$wp_styles->do_items( false );

	/**
	 * Filters whether to print the admin styles.
	 *
	 * @since 2.8.0
	 *
	 * @param bool $print Whether to print the admin styles. Default true.
	 */
	if ( apply_filters( 'print_admin_styles', true ) ) {
		_print_styles();
	}

	$wp_styles->reset();
	return $wp_styles->done;
}

/**
 * Prints the styles that were queued too late for the HTML head.
 *
 * @since 3.3.0
 *
 * @global WP_Styles $wp_styles
 * @global bool      $concatenate_scripts
 *
 * @return array|void
 */
function print_late_styles() {
	global $wp_styles, $concatenate_scripts;

	if ( ! ( $wp_styles instanceof WP_Styles ) ) {
		return;
	}

	script_concat_settings();
	$wp_styles->do_concat = $concatenate_scripts;
	$wp_styles->do_footer_items();

	/**
	 * Filters whether to print the styles queued too late for the HTML head.
	 *
	 * @since 3.3.0
	 *
	 * @param bool $print Whether to print the 'late' styles. Default true.
	 */
	if ( apply_filters( 'print_late_styles', true ) ) {
		_print_styles();
	}

	$wp_styles->reset();
	return $wp_styles->done;
}

/**
 * Print styles (internal use only)
 *
 * @ignore
 * @since 3.3.0
 *
 * @global bool $compress_css
 */
function _print_styles() {
	global $compress_css;

	$wp_styles = wp_styles();

	$zip = $compress_css ? 1 : 0;
	if ( $zip && defined( 'ENFORCE_GZIP' ) && ENFORCE_GZIP ) {
		$zip = 'gzip';
	}

	$concat    = trim( $wp_styles->concat, ', ' );
	$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';

	if ( $concat ) {
		$dir = $wp_styles->text_direction;
		$ver = $wp_styles->default_version;

		$concat       = str_split( $concat, 128 );
		$concatenated = '';

		foreach ( $concat as $key => $chunk ) {
			$concatenated .= "&load%5Bchunk_{$key}%5D={$chunk}";
		}

		$href = $wp_styles->base_url . "/wp-admin/load-styles.php?c={$zip}&dir={$dir}" . $concatenated . '&ver=' . $ver;
		echo "<link rel='stylesheet' href='" . esc_attr( $href ) . "'{$type_attr} media='all' />\n";

		if ( ! empty( $wp_styles->print_code ) ) {
			echo "<style{$type_attr}>\n";
			echo $wp_styles->print_code;
			echo "\n</style>\n";
		}
	}

	if ( ! empty( $wp_styles->print_html ) ) {
		echo $wp_styles->print_html;
	}
}

/**
 * Determine the concatenation and compression settings for scripts and styles.
 *
 * @since 2.8.0
 *
 * @global bool $concatenate_scripts
 * @global bool $compress_scripts
 * @global bool $compress_css
 */
function script_concat_settings() {
	global $concatenate_scripts, $compress_scripts, $compress_css;

	$compressed_output = ( ini_get( 'zlib.output_compression' ) || 'ob_gzhandler' === ini_get( 'output_handler' ) );

	if ( ! isset( $concatenate_scripts ) ) {
		$concatenate_scripts = defined( 'CONCATENATE_SCRIPTS' ) ? CONCATENATE_SCRIPTS : true;
		if ( ( ! is_admin() && ! did_action( 'login_init' ) ) || ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ) {
			$concatenate_scripts = false;
		}
	}

	if ( ! isset( $compress_scripts ) ) {
		$compress_scripts = defined( 'COMPRESS_SCRIPTS' ) ? COMPRESS_SCRIPTS : true;
		if ( $compress_scripts && ( ! get_site_option( 'can_compress_scripts' ) || $compressed_output ) ) {
			$compress_scripts = false;
		}
	}

	if ( ! isset( $compress_css ) ) {
		$compress_css = defined( 'COMPRESS_CSS' ) ? COMPRESS_CSS : true;
		if ( $compress_css && ( ! get_site_option( 'can_compress_scripts' ) || $compressed_output ) ) {
			$compress_css = false;
		}
	}
}

/**
 * Handles the enqueueing of block scripts and styles that are common to both
 * the editor and the front-end.
 *
 * @since 5.0.0
 *
 * @global WP_Screen $current_screen WordPress current screen object.
 */
function wp_common_block_scripts_and_styles() {
	if ( is_admin() && ! wp_should_load_block_editor_scripts_and_styles() ) {
		return;
	}

	wp_enqueue_style( 'wp-block-library' );

	if ( current_theme_supports( 'wp-block-styles' ) ) {
		wp_enqueue_style( 'wp-block-library-theme' );
	}

	/**
	 * Fires after enqueuing block assets for both editor and front-end.
	 *
	 * Call `add_action` on any hook before 'wp_enqueue_scripts'.
	 *
	 * In the function call you supply, simply use `wp_enqueue_script` and
	 * `wp_enqueue_style` to add your functionality to the Gutenberg editor.
	 *
	 * @since 5.0.0
	 */
	do_action( 'enqueue_block_assets' );
}

/**
 * Enqueues the global styles defined via theme.json.
 *
 * @since 5.8.0
 */
function wp_enqueue_global_styles() {
	if ( ! WP_Theme_JSON_Resolver::theme_has_support() ) {
		return;
	}

	$separate_assets = wp_should_load_separate_core_block_assets();

	/*
	 * Global styles should be printed in the head when loading all styles combined.
	 * The footer should only be used to print global styles for classic themes with separate core assets enabled.
	 *
	 * See https://core.trac.wordpress.org/ticket/53494.
	 */
	if ( ( ! $separate_assets && doing_action( 'wp_footer' ) ) || ( $separate_assets && doing_action( 'wp_enqueue_scripts' ) ) ) {
		return;
	}

	$can_use_cache = (
		( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) &&
		( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ) &&
		( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) &&
		! is_admin()
	);

	$stylesheet = null;
	if ( $can_use_cache ) {
		$cache = get_transient( 'global_styles' );
		if ( $cache ) {
			$stylesheet = $cache;
		}
	}

	if ( null === $stylesheet ) {
		$settings   = get_default_block_editor_settings();
		$theme_json = WP_Theme_JSON_Resolver::get_merged_data( $settings );
		$stylesheet = $theme_json->get_stylesheet();

		if ( $can_use_cache ) {
			set_transient( 'global_styles', $stylesheet, MINUTE_IN_SECONDS );
		}
	}

	if ( empty( $stylesheet ) ) {
		return;
	}

	wp_register_style( 'global-styles', false, array(), true, true );
	wp_add_inline_style( 'global-styles', $stylesheet );
	wp_enqueue_style( 'global-styles' );
}

/**
 * Checks if the editor scripts and styles for all registered block types
 * should be enqueued on the current screen.
 *
 * @since 5.6.0
 *
 * @return bool Whether scripts and styles should be enqueued.
 */
function wp_should_load_block_editor_scripts_and_styles() {
	global $current_screen;

	$is_block_editor_screen = ( $current_screen instanceof WP_Screen ) && $current_screen->is_block_editor();

	/**
	 * Filters the flag that decides whether or not block editor scripts and styles
	 * are going to be enqueued on the current screen.
	 *
	 * @since 5.6.0
	 *
	 * @param bool $is_block_editor_screen Current value of the flag.
	 */
	return apply_filters( 'should_load_block_editor_scripts_and_styles', $is_block_editor_screen );
}

/**
 * Checks whether separate styles should be loaded for core blocks on-render.
 *
 * When this function returns true, other functions ensure that core blocks
 * only load their assets on-render, and each block loads its own, individual
 * assets. Third-party blocks only load their assets when rendered.
 *
 * When this function returns false, all core block assets are loaded regardless
 * of whether they are rendered in a page or not, because they are all part of
 * the `block-library/style.css` file. Assets for third-party blocks are always
 * enqueued regardless of whether they are rendered or not.
 *
 * This only affects front end and not the block editor screens.
 *
 * @see wp_enqueue_registered_block_scripts_and_styles()
 * @see register_block_style_handle()
 *
 * @since 5.8.0
 *
 * @return bool Whether separate assets will be loaded.
 */
function wp_should_load_separate_core_block_assets() {
	if ( is_admin() || is_feed() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return false;
	}

	/**
	 * Filters whether block styles should be loaded separately.
	 *
	 * Returning false loads all core block assets, regardless of whether they are rendered
	 * in a page or not. Returning true loads core block assets only when they are rendered.
	 *
	 * @since 5.8.0
	 *
	 * @param bool $load_separate_assets Whether separate assets will be loaded.
	 *                                   Default false (all block assets are loaded, even when not used).
	 */
	return apply_filters( 'should_load_separate_core_block_assets', false );
}

/**
 * Enqueues registered block scripts and styles, depending on current rendered
 * context (only enqueuing editor scripts while in context of the editor).
 *
 * @since 5.0.0
 *
 * @global WP_Screen $current_screen WordPress current screen object.
 */
function wp_enqueue_registered_block_scripts_and_styles() {
	global $current_screen;

	if ( wp_should_load_separate_core_block_assets() ) {
		return;
	}

	$load_editor_scripts = is_admin() && wp_should_load_block_editor_scripts_and_styles();

	$block_registry = WP_Block_Type_Registry::get_instance();
	foreach ( $block_registry->get_all_registered() as $block_name => $block_type ) {
		// Front-end styles.
		if ( ! empty( $block_type->style ) ) {
			wp_enqueue_style( $block_type->style );
		}

		// Front-end script.
		if ( ! empty( $block_type->script ) ) {
			wp_enqueue_script( $block_type->script );
		}

		// Editor styles.
		if ( $load_editor_scripts && ! empty( $block_type->editor_style ) ) {
			wp_enqueue_style( $block_type->editor_style );
		}

		// Editor script.
		if ( $load_editor_scripts && ! empty( $block_type->editor_script ) ) {
			wp_enqueue_script( $block_type->editor_script );
		}
	}
}

/**
 * Function responsible for enqueuing the styles required for block styles functionality on the editor and on the frontend.
 *
 * @since 5.3.0
 */
function enqueue_block_styles_assets() {
	$block_styles = WP_Block_Styles_Registry::get_instance()->get_all_registered();

	foreach ( $block_styles as $block_name => $styles ) {
		foreach ( $styles as $style_properties ) {
			if ( isset( $style_properties['style_handle'] ) ) {

				// If the site loads separate styles per-block, enqueue the stylesheet on render.
				if ( wp_should_load_separate_core_block_assets() ) {
					add_filter(
						'render_block',
						function( $html ) use ( $style_properties ) {
							wp_enqueue_style( $style_properties['style_handle'] );
							return $html;
						}
					);
				} else {
					wp_enqueue_style( $style_properties['style_handle'] );
				}
			}
			if ( isset( $style_properties['inline_style'] ) ) {

				// Default to "wp-block-library".
				$handle = 'wp-block-library';

				// If the site loads separate styles per-block, check if the block has a stylesheet registered.
				if ( wp_should_load_separate_core_block_assets() ) {
					$block_stylesheet_handle = generate_block_asset_handle( $block_name, 'style' );
					global $wp_styles;
					if ( isset( $wp_styles->registered[ $block_stylesheet_handle ] ) ) {
						$handle = $block_stylesheet_handle;
					}
				}

				// Add inline styles to the calculated handle.
				wp_add_inline_style( $handle, $style_properties['inline_style'] );
			}
		}
	}
}

/**
 * Function responsible for enqueuing the assets required for block styles functionality on the editor.
 *
 * @since 5.3.0
 */
function enqueue_editor_block_styles_assets() {
	$block_styles = WP_Block_Styles_Registry::get_instance()->get_all_registered();

	$register_script_lines = array( '( function() {' );
	foreach ( $block_styles as $block_name => $styles ) {
		foreach ( $styles as $style_properties ) {
			$block_style = array(
				'name'  => $style_properties['name'],
				'label' => $style_properties['label'],
			);
			if ( isset( $style_properties['is_default'] ) ) {
				$block_style['isDefault'] = $style_properties['is_default'];
			}
			$register_script_lines[] = sprintf(
				'	wp.blocks.registerBlockStyle( \'%s\', %s );',
				$block_name,
				wp_json_encode( $block_style )
			);
		}
	}
	$register_script_lines[] = '} )();';
	$inline_script           = implode( "\n", $register_script_lines );

	wp_register_script( 'wp-block-styles', false, array( 'wp-blocks' ), true, true );
	wp_add_inline_script( 'wp-block-styles', $inline_script );
	wp_enqueue_script( 'wp-block-styles' );
}

/**
 * Enqueues the assets required for the block directory within the block editor.
 *
 * @since 5.5.0
 */
function wp_enqueue_editor_block_directory_assets() {
	wp_enqueue_script( 'wp-block-directory' );
	wp_enqueue_style( 'wp-block-directory' );
}

/**
 * Enqueues the assets required for the format library within the block editor.
 *
 * @since 5.8.0
 */
function wp_enqueue_editor_format_library_assets() {
	wp_enqueue_script( 'wp-format-library' );
	wp_enqueue_style( 'wp-format-library' );
}

/**
 * Sanitizes an attributes array into an attributes string to be placed inside a `<script>` tag.
 *
 * Automatically injects type attribute if needed.
 * Used by {@see wp_get_script_tag()} and {@see wp_get_inline_script_tag()}.
 *
 * @since 5.7.0
 *
 * @param array $attributes Key-value pairs representing `<script>` tag attributes.
 * @return string String made of sanitized `<script>` tag attributes.
 */
function wp_sanitize_script_attributes( $attributes ) {
	$html5_script_support = ! is_admin() && ! current_theme_supports( 'html5', 'script' );
	$attributes_string    = '';

	// If HTML5 script tag is supported, only the attribute name is added
	// to $attributes_string for entries with a boolean value, and that are true.
	foreach ( $attributes as $attribute_name => $attribute_value ) {
		if ( is_bool( $attribute_value ) ) {
			if ( $attribute_value ) {
				$attributes_string .= $html5_script_support ? sprintf( ' %1$s="%2$s"', esc_attr( $attribute_name ), esc_attr( $attribute_name ) ) : ' ' . esc_attr( $attribute_name );
			}
		} else {
			$attributes_string .= sprintf( ' %1$s="%2$s"', esc_attr( $attribute_name ), esc_attr( $attribute_value ) );
		}
	}

	return $attributes_string;
}

/**
 * Formats `<script>` loader tags.
 *
 * It is possible to inject attributes in the `<script>` tag via the {@see 'wp_script_attributes'} filter.
 * Automatically injects type attribute if needed.
 *
 * @since 5.7.0
 *
 * @param array $attributes Key-value pairs representing `<script>` tag attributes.
 * @return string String containing `<script>` opening and closing tags.
 */
function wp_get_script_tag( $attributes ) {
	if ( ! isset( $attributes['type'] ) && ! is_admin() && ! current_theme_supports( 'html5', 'script' ) ) {
		$attributes['type'] = 'text/javascript';
	}
	/**
	 * Filters attributes to be added to a script tag.
	 *
	 * @since 5.7.0
	 *
	 * @param array $attributes Key-value pairs representing `<script>` tag attributes.
	 *                          Only the attribute name is added to the `<script>` tag for
	 *                          entries with a boolean value, and that are true.
	 */
	$attributes = apply_filters( 'wp_script_attributes', $attributes );

	return sprintf( "<script%s></script>\n", wp_sanitize_script_attributes( $attributes ) );
}

/**
 * Prints formatted `<script>` loader tag.
 *
 * It is possible to inject attributes in the `<script>` tag via the  {@see 'wp_script_attributes'}  filter.
 * Automatically injects type attribute if needed.
 *
 * @since 5.7.0
 *
 * @param array $attributes Key-value pairs representing `<script>` tag attributes.
 */
function wp_print_script_tag( $attributes ) {
	echo wp_get_script_tag( $attributes );
}

/**
 * Wraps inline JavaScript in `<script>` tag.
 *
 * It is possible to inject attributes in the `<script>` tag via the  {@see 'wp_script_attributes'}  filter.
 * Automatically injects type attribute if needed.
 *
 * @since 5.7.0
 *
 * @param string $javascript Inline JavaScript code.
 * @param array  $attributes  Optional. Key-value pairs representing `<script>` tag attributes.
 * @return string String containing inline JavaScript code wrapped around `<script>` tag.
 */
function wp_get_inline_script_tag( $javascript, $attributes = array() ) {
	if ( ! isset( $attributes['type'] ) && ! is_admin() && ! current_theme_supports( 'html5', 'script' ) ) {
		$attributes['type'] = 'text/javascript';
	}
	/**
	 * Filters attributes to be added to a script tag.
	 *
	 * @since 5.7.0
	 *
	 * @param array $attributes Key-value pairs representing `<script>` tag attributes.
	 *                          Only the attribute name is added to the `<script>` tag for
	 *                          entries with a boolean value, and that are true.
	 */
	$attributes = apply_filters( 'wp_inline_script_attributes', $attributes, $javascript );

	$javascript = "\n" . trim( $javascript, "\n\r " ) . "\n";

	return sprintf( "<script%s>%s</script>\n", wp_sanitize_script_attributes( $attributes ), $javascript );
}

/**
 * Prints inline JavaScript wrapped in `<script>` tag.
 *
 * It is possible to inject attributes in the `<script>` tag via the  {@see 'wp_script_attributes'}  filter.
 * Automatically injects type attribute if needed.
 *
 * @since 5.7.0
 *
 * @param string $javascript Inline JavaScript code.
 * @param array  $attributes Optional. Key-value pairs representing `<script>` tag attributes.
 */
function wp_print_inline_script_tag( $javascript, $attributes = array() ) {
	echo wp_get_inline_script_tag( $javascript, $attributes );
}

/**
 * Allows small styles to be inlined.
 *
 * This improves performance and sustainability, and is opt-in. Stylesheets can opt in
 * by adding `path` data using `wp_style_add_data`, and defining the file's absolute path:
 *
 *     wp_style_add_data( $style_handle, 'path', $file_path );
 *
 * @since 5.8.0
 *
 * @global WP_Styles $wp_styles
 */
function wp_maybe_inline_styles() {
	global $wp_styles;

	$total_inline_limit = 20000;
	/**
	 * The maximum size of inlined styles in bytes.
	 *
	 * @since 5.8.0
	 *
	 * @param int $total_inline_limit The file-size threshold, in bytes. Default 20000.
	 */
	$total_inline_limit = apply_filters( 'styles_inline_size_limit', $total_inline_limit );

	$styles = array();

	// Build an array of styles that have a path defined.
	foreach ( $wp_styles->queue as $handle ) {
		if ( wp_styles()->get_data( $handle, 'path' ) && file_exists( $wp_styles->registered[ $handle ]->extra['path'] ) ) {
			$styles[] = array(
				'handle' => $handle,
				'path'   => $wp_styles->registered[ $handle ]->extra['path'],
				'size'   => filesize( $wp_styles->registered[ $handle ]->extra['path'] ),
			);
		}
	}

	if ( ! empty( $styles ) ) {
		// Reorder styles array based on size.
		usort(
			$styles,
			function( $a, $b ) {
				return ( $a['size'] <= $b['size'] ) ? -1 : 1;
			}
		);

		/*
		 * The total inlined size.
		 *
		 * On each iteration of the loop, if a style gets added inline the value of this var increases
		 * to reflect the total size of inlined styles.
		 */
		$total_inline_size = 0;

		// Loop styles.
		foreach ( $styles as $style ) {

			// Size check. Since styles are ordered by size, we can break the loop.
			if ( $total_inline_size + $style['size'] > $total_inline_limit ) {
				break;
			}

			// Get the styles if we don't already have them.
			$style['css'] = file_get_contents( $style['path'] );

			// Set `src` to `false` and add styles inline.
			$wp_styles->registered[ $style['handle'] ]->src = false;
			if ( empty( $wp_styles->registered[ $style['handle'] ]->extra['after'] ) ) {
				$wp_styles->registered[ $style['handle'] ]->extra['after'] = array();
			}
			array_unshift( $wp_styles->registered[ $style['handle'] ]->extra['after'], $style['css'] );

			// Add the styles size to the $total_inline_size var.
			$total_inline_size += (int) $style['size'];
		}
	}
}

/**
 * Inject the block editor assets that need to be loaded into the editor's iframe as an inline script.
 *
 * @since 5.8.0
 */
function wp_add_iframed_editor_assets_html() {
	if ( ! wp_should_load_block_editor_scripts_and_styles() ) {
		return;
	}

	$script_handles = array();
	$style_handles  = array(
		'wp-block-editor',
		'wp-block-library',
		'wp-block-library-theme',
		'wp-edit-blocks',
	);

	$block_registry = WP_Block_Type_Registry::get_instance();

	foreach ( $block_registry->get_all_registered() as $block_type ) {
		if ( ! empty( $block_type->style ) ) {
			$style_handles[] = $block_type->style;
		}

		if ( ! empty( $block_type->editor_style ) ) {
			$style_handles[] = $block_type->editor_style;
		}

		if ( ! empty( $block_type->script ) ) {
			$script_handles[] = $block_type->script;
		}
	}

	$style_handles = array_unique( $style_handles );
	$done          = wp_styles()->done;

	ob_start();

	wp_styles()->done = array();
	wp_styles()->do_items( $style_handles );
	wp_styles()->done = $done;

	$styles = ob_get_clean();

	$script_handles = array_unique( $script_handles );
	$done           = wp_scripts()->done;

	ob_start();

	wp_scripts()->done = array();
	wp_scripts()->do_items( $script_handles );
	wp_scripts()->done = $done;

	$scripts = ob_get_clean();

	$editor_assets = wp_json_encode(
		array(
			'styles'  => $styles,
			'scripts' => $scripts,
		)
	);

	echo "<script>window.__editorAssets = $editor_assets</script>";
}
