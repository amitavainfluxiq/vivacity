<?php
global $AI;
$theme = $AI->skin->vars['theme'];
$page = $AI->skin->vars['pagename'];


//BODY
if( ($path=ai_cascadepath('system/themes/simpl/pages/'.AI_PAGE_NAME.'.php'))!='' ) require($path);
else {
	echo $AI->skin->get_tag_dynalist('body');
}




/*$pagename = AI_PAGE_NAME;

//IF IT HAS A PAGES FILE, USE THAT
if(file_exists('system/themes/simpl/pages/'.$pagename.'.php')){
	require('system/themes/simpl/pages/'.$pagename.'.php');	
}
else {
	echo $AI->get_dynamic_area('Page-'.$pagename);
}*/
