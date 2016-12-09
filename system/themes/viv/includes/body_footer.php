<?php
global $AI;
$theme = $AI->skin->vars['theme'];
$page = $AI->skin->vars['pagename'];

$replacements=array();

if( ($path=ai_cascadepath('system/themes/'.$theme.'/pages/'.AI_PAGE_NAME.'-body_footer.php'))!='' ) require($path);
else {
	echo $AI->skin->get_tag_dynalist('body_footer');
	//SUPPORT REPLACEMENTS IN BODY_FOOTER? LIKE FOR MENUs and such?
}


