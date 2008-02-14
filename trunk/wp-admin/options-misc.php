<?php
require_once('admin.php');

$title = __('Miscellaneous Settings');
$parent_file = 'options-general.php';

include('admin-header.php');

?>

<div class="wrap">
<h2><?php _e('Miscellaneous Settings') ?></h2>
<form method="post" action="options.php">
<?php wp_nonce_field('update-options') ?>
<h3><?php _e('Uploading'); ?></h3>
<table class="niceblue">
<tr valign="top">
<th scope="row"><?php _e('Store uploads in this folder'); ?>:</th>
<td><input name="upload_path" type="text" id="upload_path" class="code" value="<?php echo attribute_escape(str_replace(ABSPATH, '', get_option('upload_path'))); ?>" size="40" />
<br />
<?php _e('Default is <code>wp-content/uploads</code>'); ?>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Full URL path to files (optional)'); ?>:</th>
<td><input name="upload_url_path" type="text" id="upload_url_path" class="code" value="<?php echo attribute_escape( get_option('upload_url_path')); ?>" size="40" />
</td>
</tr>

<tr>
<td></td>
<td>
<label for="uploads_use_yearmonth_folders">
<input name="uploads_use_yearmonth_folders" type="checkbox" id="uploads_use_yearmonth_folders" value="1" <?php checked('1', get_option('uploads_use_yearmonth_folders')); ?> />
<?php _e('Organize my uploads into month- and year-based folders'); ?>
</label>
</td>
</tr>
</table>

<p><input name="use_linksupdate" type="checkbox" id="use_linksupdate" value="1" <?php checked('1', get_option('use_linksupdate')); ?> />
<label for="use_linksupdate"><?php _e('Track Links&#8217; Update Times') ?></label></p>
<p>
<label><input type="checkbox" name="hack_file" value="1" <?php checked('1', get_option('hack_file')); ?> /> <?php _e('Use legacy <code>my-hacks.php</code> file support') ?></label>
</p>

<p class="submit">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="hack_file,use_linksupdate,uploads_use_yearmonth_folders,upload_path,upload_url_path" />
<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
</p>
</form>
</div>

<?php include('./admin-footer.php'); ?>