<?php
$mode = 'bookmarklet';

$standalone = 1;
require_once('admin-header.php');

if ($user_level == 0)
	die ("Cheatin' uh?");

if ('b' == $a) {

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript">
<!--
window.close()
-->
</script>
</head>
<body></body>
</html>
<?php
} else {
    $popuptitle = htmlspecialchars(stripslashes($popuptitle));
    $text = htmlspecialchars(stripslashes(urldecode($text)));
    
    /* big funky fixes for browsers' javascript bugs */
    
    if (($is_macIE) && (!isset($IEMac_bookmarklet_fix))) {
        $popuptitle = preg_replace($wp_macIE_correction["in"],$wp_macIE_correction["out"],$popuptitle);
        $text = preg_replace($wp_macIE_correction["in"],$wp_macIE_correction["out"],$text);
    }
    
    if (($is_winIE) && (!isset($IEWin_bookmarklet_fix))) {
        $popuptitle =  preg_replace("/\%u([0-9A-F]{4,4})/e",  "'&#'.base_convert('\\1',16,10).';'", $popuptitle);
        $text =  preg_replace("/\%u([0-9A-F]{4,4})/e",  "'&#'.base_convert('\\1',16,10).';'", $text);
    }
    
    if (($is_gecko) && (!isset($Gecko_bookmarklet_fix))) {
        $popuptitle = preg_replace($wp_gecko_correction["in"],$wp_gecko_correction["out"],$popuptitle);
        $text = preg_replace($wp_gecko_correction["in"],$wp_gecko_correction["out"],$text);
    }
    
    $post_title = $_REQUEST['post_title'];
    if (!empty($post_title)) {
        $post_title =  stripslashes($post_title);
    } else {
        $post_title = $popuptitle;
    }
// I'm not sure why we're using $edited_post_title in the edit-form.php, but we are
// and that is what is being included below. For this reason, I am just duplicating
// the var instead of changing the assignment on the lines above. 
// -- Alex King 2004-01-07
    $edited_post_title = htmlspecialchars($post_title);

// $post_pingback needs to be set in any file that includes edit-form.php
    $post_pingback = get_settings('default_pingback_flag');
    
    $content = $_REQUEST['content'];
    if (!empty($content)) {
        $content =  stripslashes($content);
    } else {
        $content = '<a href="'.$popupurl.'">'.$popuptitle.'</a>'."\n$text";
    }
    
    /* /big funky fixes */

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php bloginfo('name') ?> &rsaquo; Bookmarklet &#8212; WordPress</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_settings('blog_charset'); ?>" />
<link rel="stylesheet" href="wp-admin.css" type="text/css" />
<link rel="shortcut icon" href="../wp-images/wp-favicon.png" />
<script type="text/javascript">
<!--
function launchupload() {
	window.open ("upload.php", "wpupload", "width=380,height=360,location=0,menubar=0,resizable=1,scrollbars=yes,status=1,toolbar=0");
}

//-->
</script>
<style type="text/css">
<!--

#wpbookmarklet textarea,input,select {
	border-width: 1px;
	border-color: #cccccc;
	border-style: solid;
	padding: 2px;
	margin: 1px;
}

#wpbookmarklet .checkbox {
	background-color: #ffffff;
	border-width: 0px;
	padding: 0px;
	margin: 0px;
}

#wpbookmarklet textarea {
	font-family: Verdana, Geneva, Arial, Helvetica;
	font-size: 0.9em;
}

#wpbookmarklet .wrap {
    border: 0px;
}

#wpbookmarklet #postdiv {
    margin-bottom: 0.5em;
}

#wpbookmarklet #titlediv {
    margin-bottom: 1em;
}

-->
</style>
</head>
<body id="wpbookmarklet">
<div id="wphead">
<h1><?php bloginfo('name') ?></h1>
</div>

<?php require('edit-form.php'); ?>

</body>
</html><?php
}
?>
