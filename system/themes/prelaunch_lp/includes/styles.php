<?php
/*
  In addition to needed styles and scripts
	The styles.php file should draw/include these standard items for the new standardized theme system:
	1. system/themes/$theme/css/styles.css 
	2. system/themes/$theme/js/scripts.js
	3. system/themes/$theme/includes/head.html (drawn to output within <head></head> tags)
*/


	global $AI;
	//include site styles
	//require_once(ai_cascadepath('includes/stylesheets.php'));

	$theme = $AI->skin->vars['theme'];
	$page = $AI->skin->vars['pagename'];

	//BOOTSTRAP WILL BE INCLUDED BY SKIN CLASS BECAUSE theme.info CONFIG FILE REQUESTS IT
	//  (see theme.info vars:    uses_bootstrap = true;    uses_own_bootstrap = false;  )

	//DRAW BASIC AI STYLES, JQUERY, ETC AND OTHER INCLUDES (FOR DYNAMIC AREAS ETC)
	require_once(ai_cascadepath('includes/stylesheets_min.php'));

	//ALSO JQUERY UI
	$AI->skin->js('includes/core/js/jquery-ui/jquery-ui.min.js', 'high');
	$AI->skin->css('includes/core/js/jquery-ui/jquery-ui.css');

	//Optional Form styles
	//$AI->skin->css('includes/plugins/bootstrap/s-legacy-forms.css','high');
	
	//Optional Bootstrap Adjustment styles
	//$AI->skin->css('includes/plugins/bootstrap/bootstrap_adjust.css','high');
	
	$AI->skin->css('css/ai.commerce.css','low');
	$AI->skin->css('css/ai_min.css','low');
	$AI->skin->css('css/ai_min_buttons.css','low');
	
	
	/* ???? MATCH HEIGHT *
	[10:32:47 AM] Jon Agbayani:  I've done something similar to matchHeight, creating a matchViewport jQuery plugin so that on MePower the slider would match the browsers' size so it would fill the screen.
	[11:05:16 AM] Andrew Lovelace: Cool. Is that something we could add in on every page (in a default theme) without much cost?
	[11:08:01 AM] Jon Agbayani: Shouldn't cost much at all.  My version is only 25 lines and is just definitions, no objects created until actually used.  I'm not sure exactly how matchHeight works, it might have more options/features.  But it too is mostly definition statemetns.
	
	includes/js/ai_match_height.js
	??
	*/
	
	
	//Not sure if we should use this on all themes
	//We probably want to standardize our DA images to use bootstrap theme classes
	//$AI->skin->css('includes/plugins/bootstrap/bootstrap_adjust.css','low');
	
	//Default CSS File, empty by default
	$AI->skin->css('system/themes/'.$theme.'/css/styles.css','low');
	//Default JS File, empty by default
	$AI->skin->js('system/themes/'.$theme.'/js/scripts.js','low');

	
	//INCLUDE SPECIAL CSS FOR INTERNAL (Non-Anon) PAGES ?
	if($AI->user->userID>0) {
		$groups_with_perm = $AI->perm->get_groups_with_access_to( util_pagename_to_permission_name( AI_PAGE_NAME ) );
		if(!in_array('Anonymous',$groups_with_perm)) {
			$AI->skin->css('system/themes/'.$theme.'/css/internal_pages.css','low');
		}
	}




?>

<?php 
$headhtml_file = ai_cascadepath('system/themes/'.$theme.'/includes/head.html');
if($headhtml_file!='') readfile($headhtml_file);
