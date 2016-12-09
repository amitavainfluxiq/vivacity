<?php
global $AI;
$theme = $AI->skin->vars['theme'];
$page = $AI->skin->vars['pagename'];

$replacements=array();

if( ($path=ai_cascadepath('system/themes/'.$theme.'/pages/'.AI_PAGE_NAME.'-body_header.php'))!='' ) require($path);
else {
	echo $AI->skin->get_tag_dynalist('body_header');
	//SUPPORT REPLACEMENTS IN BODY_HEADER? LIKE FOR SUB-MENUs?
}


