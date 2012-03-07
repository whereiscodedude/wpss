<?php
/**
 * WordPress Theme Install Administration API
 *
 * @package WordPress
 * @subpackage Administration
 */

$themes_allowedtags = array('a' => array('href' => array(), 'title' => array(), 'target' => array()),
	'abbr' => array('title' => array()), 'acronym' => array('title' => array()),
	'code' => array(), 'pre' => array(), 'em' => array(), 'strong' => array(),
	'div' => array(), 'p' => array(), 'ul' => array(), 'ol' => array(), 'li' => array(),
	'h1' => array(), 'h2' => array(), 'h3' => array(), 'h4' => array(), 'h5' => array(), 'h6' => array(),
	'img' => array('src' => array(), 'class' => array(), 'alt' => array())
);

$theme_field_defaults = array( 'description' => true, 'sections' => false, 'tested' => true, 'requires' => true,
	'rating' => true, 'downloaded' => true, 'downloadlink' => true, 'last_updated' => true, 'homepage' => true,
	'tags' => true, 'num_ratings' => true
);

/**
 * Retrieve list of WordPress theme features (aka theme tags)
 *
 * @since 2.8.0
 *
 * @deprecated since 3.1.0 Use get_theme_feature_list() instead.
 *
 * @return array
 */
function install_themes_feature_list( ) {
	if ( !$cache = get_transient( 'wporg_theme_feature_list' ) )
		set_transient( 'wporg_theme_feature_list', array( ), 10800);

	if ( $cache )
		return $cache;

	$feature_list = themes_api( 'feature_list', array( ) );
	if ( is_wp_error( $feature_list ) )
		return $features;

	set_transient( 'wporg_theme_feature_list', $feature_list, 10800 );

	return $feature_list;
}

/**
 * Display search form for searching themes.
 *
 * @since 2.8.0
 */
function install_theme_search_form() {
	$type = isset( $_REQUEST['type'] ) ? stripslashes( $_REQUEST['type'] ) : '';
	$term = isset( $_REQUEST['s'] ) ? stripslashes( $_REQUEST['s'] ) : '';
	?>
<p class="install-help"><?php _e('Search for themes by keyword, author, or tag.') ?></p>

<form id="search-themes" method="get" action="">
	<input type="hidden" name="tab" value="search" />
	<select	name="type" id="typeselector">
	<option value="term" <?php selected('term', $type) ?>><?php _e('Term'); ?></option>
	<option value="author" <?php selected('author', $type) ?>><?php _e('Author'); ?></option>
	<option value="tag" <?php selected('tag', $type) ?>><?php _ex('Tag', 'Theme Installer'); ?></option>
	</select>
	<input type="text" name="s" size="30" value="<?php echo esc_attr($term) ?>" />
	<?php submit_button( __( 'Search' ), 'button', 'search', false ); ?>
</form>
<?php
}

/**
 * Display tags filter for themes.
 *
 * @since 2.8.0
 */
function install_themes_dashboard() {
	install_theme_search_form();
?>
<h4><?php _e('Feature Filter') ?></h4>
<p class="install-help"><?php _e('Find a theme based on specific features') ?></p>

<form method="get" action="">
	<input type="hidden" name="tab" value="search" />
	<?php
	$feature_list = get_theme_feature_list( );
	echo '<div class="feature-filter">';

	foreach ( (array) $feature_list as $feature_name => $features ) {
		$feature_name = esc_html( $feature_name );
		echo '<div class="feature-name">' . $feature_name . '</div>';

		echo '<ol class="feature-group">';
		foreach ( $features as $feature => $feature_name ) {
			$feature_name = esc_html( $feature_name );
			$feature = esc_attr($feature);
?>

<li>
	<input type="checkbox" name="features[]" id="feature-id-<?php echo $feature; ?>" value="<?php echo $feature; ?>" />
	<label for="feature-id-<?php echo $feature; ?>"><?php echo $feature_name; ?></label>
</li>

<?php	} ?>
</ol>
<br class="clear" />
<?php
	} ?>

</div>
<br class="clear" />
<?php submit_button( __( 'Find Themes' ), 'button', 'search' ); ?>
</form>
<?php
}
add_action('install_themes_dashboard', 'install_themes_dashboard');

function install_themes_upload($page = 1) {
?>
<h4><?php _e('Install a theme in .zip format') ?></h4>
<p class="install-help"><?php _e('If you have a theme in a .zip format, you may install it by uploading it here.') ?></p>
<form method="post" enctype="multipart/form-data" action="<?php echo self_admin_url('update.php?action=upload-theme') ?>">
	<?php wp_nonce_field( 'theme-upload') ?>
	<input type="file" name="themezip" />
	<?php submit_button( __( 'Install Now' ), 'button', 'install-theme-submit', false ); ?>
</form>
	<?php
}
add_action('install_themes_upload', 'install_themes_upload', 10, 1);

/*
 * Prints a theme on the Install Themes pages.
 *
 * @param object $theme An object that contains theme data returned by the WordPress.org API.
 *
 * Example theme data:
 *   object(stdClass)[59]
 *     public 'name' => string 'Magazine Basic' (length=14)
 *     public 'slug' => string 'magazine-basic' (length=14)
 *     public 'version' => string '1.1' (length=3)
 *     public 'author' => string 'tinkerpriest' (length=12)
 *     public 'preview_url' => string 'http://wp-themes.com/?magazine-basic' (length=36)
 *     public 'screenshot_url' => string 'http://wp-themes.com/wp-content/themes/magazine-basic/screenshot.png' (length=68)
 *     public 'rating' => float 80
 *     public 'num_ratings' => int 1
 *     public 'homepage' => string 'http://wordpress.org/extend/themes/magazine-basic' (length=49)
 *     public 'description' => string 'A basic magazine style layout with a fully customizable layout through a backend interface. Designed by <a href="http://bavotasan.com">c.bavota</a> of <a href="http://tinkerpriestmedia.com">Tinker Priest Media</a>.' (length=214)
 *     public 'download_link' => string 'http://wordpress.org/extend/themes/download/magazine-basic.1.1.zip' (length=66)
 */
function display_theme( $theme ) {
	global $themes_allowedtags;

	if ( empty( $theme ) )
		return;

	$name   = wp_kses( $theme->name,   $themes_allowedtags );
	$author = wp_kses( $theme->author, $themes_allowedtags );

	$num_ratings = sprintf( _n( '(based on %s rating)', '(based on %s ratings)', $theme->num_ratings ), number_format_i18n( $theme->num_ratings ) );

	$preview_url   = add_query_arg( 'theme_preview', '1' );
	$preview_title = sprintf( __('Preview &#8220;%s&#8221;'), $name );

	$install_url = add_query_arg( array(
		'action' => 'install-theme',
		'theme'  => $theme->slug,
	), self_admin_url( 'update.php' ) );

	?>
	<a class="screenshot" href="<?php echo esc_url( $preview_url ); ?>" title="<?php echo esc_attr( $preview_title ); ?>">
		<img src='<?php echo esc_url( $theme->screenshot_url ); ?>' width='150' />
	</a>

	<h3><?php
		/* translators: 1: theme name, 2: author name */
		printf( __( '%1$s <span>by %2$s</span>' ), $name, $author );
	?></h3>

	<div class="install-theme-info">
		<a class="theme-install button-primary" href="<?php echo wp_nonce_url( $install_url, 'install-theme_' . $theme->slug ); ?>"><?php _e( 'Install' ); ?></a>
		<h3 class="theme-name"><?php echo $name; ?></h3>
		<span class="theme-by"><?php printf( __( 'By %s' ), $author ); ?></span>
		<img class="theme-screenshot" src="<?php echo esc_url( $theme->screenshot_url ); ?>" />
		<div class="theme-rating" title="<?php echo esc_attr( $num_ratings ); ?>">
			<div style="width:<?php echo esc_attr( intval( $theme->rating ) . 'px' ); ?>;"></div>
		</div>
		<div class="theme-version">
			<strong><?php _e('Version:') ?> </strong>
			<?php echo wp_kses( $theme->version, $themes_allowedtags ); ?>
		</div>
		<div class="theme-description">
			<?php echo wp_kses( $theme->description, $themes_allowedtags ); ?>
		</div>
		<input class="theme-preview-url" type="hidden" value="<?php echo esc_url( $theme->preview_url ); ?>" />
	</div>
	<?php
}

/**
 * Display theme content based on theme list.
 *
 * @since 2.8.0
 */
function display_themes() {
	global $wp_list_table;

	$wp_list_table->display();
}
add_action('install_themes_search', 'display_themes');
add_action('install_themes_featured', 'display_themes');
add_action('install_themes_new', 'display_themes');
add_action('install_themes_updated', 'display_themes');

/**
 * Display theme information in dialog box form.
 *
 * @since 2.8.0
 */
function install_theme_information() {
	//TODO: This function needs a LOT of UI work :)
	global $tab, $themes_allowedtags;

	$api = themes_api('theme_information', array('slug' => stripslashes( $_REQUEST['theme'] ) ));

	if ( is_wp_error($api) )
		wp_die($api);

	// Sanitize HTML
	foreach ( (array)$api->sections as $section_name => $content )
		$api->sections[$section_name] = wp_kses($content, $themes_allowedtags);

	foreach ( array('version', 'author', 'requires', 'tested', 'homepage', 'downloaded', 'slug') as $key ) {
		if ( isset($api->$key) )
			$api->$key = wp_kses($api->$key, $themes_allowedtags);
	}

	iframe_header( __('Theme Install') );

	if ( empty($api->download_link) ) {
		echo '<div id="message" class="error"><p>' . __('<strong>ERROR:</strong> This theme is currently not available. Please try again later.') . '</p></div>';
		iframe_footer();
		exit;
	}

	if ( !empty($api->tested) && version_compare($GLOBALS['wp_version'], $api->tested, '>') )
		echo '<div class="updated"><p>' . __('<strong>Warning:</strong> This theme has <strong>not been tested</strong> with your current version of WordPress.') . '</p></div>';
	else if ( !empty($api->requires) && version_compare($GLOBALS['wp_version'], $api->requires, '<') )
		echo '<div class="updated"><p>' . __('<strong>Warning:</strong> This theme has not been marked as <strong>compatible</strong> with your version of WordPress.') . '</p></div>';

	// Default to a "new" theme
	$type = 'install';
	// Check to see if this theme is known to be installed, and has an update awaiting it.
	$update_themes = get_site_transient('update_themes');
	if ( is_object($update_themes) && isset($update_themes->response) ) {
		foreach ( (array)$update_themes->response as $theme_slug => $theme_info ) {
			if ( $theme_slug === $api->slug ) {
				$type = 'update_available';
				$update_file = $theme_slug;
				break;
			}
		}
	}

	$theme = wp_get_theme( $api->slug );
	if ( is_a( $theme, 'WP_Theme' ) ) {
		switch ( version_compare( $theme->get('Version'), $api->version ) ) {
			case 0; // equal
				$type = 'latest_installed';
			case 1: // installed theme > api version
				$type = 'newer_installed';
				$newer_version = $theme->get('Version');
		}
	}
?>

<div class='available-theme'>
<img src='<?php echo esc_url($api->screenshot_url) ?>' width='300' class="theme-preview-img" />
<h3><?php echo $api->name; ?></h3>
<p><?php printf(__('by %s'), $api->author); ?></p>
<p><?php printf(__('Version: %s'), $api->version); ?></p>

<?php
$buttons = '<a class="button" id="cancel" href="#" onclick="tb_close();return false;">' . __('Cancel') . '</a> ';

switch ( $type ) {
default:
case 'install':
	if ( current_user_can('install_themes') ) :
	$buttons .= '<a class="button-primary" id="install" href="' . wp_nonce_url(self_admin_url('update.php?action=install-theme&theme=' . $api->slug), 'install-theme_' . $api->slug) . '" target="_parent">' . __('Install Now') . '</a>';
	endif;
	break;
case 'update_available':
	if ( current_user_can('update_themes') ) :
	$buttons .= '<a class="button-primary" id="install"	href="' . wp_nonce_url(self_admin_url('update.php?action=upgrade-theme&theme=' . $update_file), 'upgrade-theme_' . $update_file) . '" target="_parent">' . __('Install Update Now') . '</a>';
	endif;
	break;
case 'newer_installed':
	if ( current_user_can('install_themes') || current_user_can('update_themes') ) :
	?><p><?php printf(__('Newer version (%s) is installed.'), $newer_version); ?></p><?php
	endif;
	break;
case 'latest_installed':
	if ( current_user_can('install_themes') || current_user_can('update_themes') ) :
	?><p><?php _e('This version is already installed.'); ?></p><?php
	endif;
	break;
} ?>
<br class="clear" />
</div>

<p class="action-button">
<?php echo $buttons; ?>
<br class="clear" />
</p>

<?php
	iframe_footer();
	exit;
}
add_action('install_themes_pre_theme-information', 'install_theme_information');
