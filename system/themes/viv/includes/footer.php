<?php
global $AI;
$theme = $AI->skin->vars['theme'];
$page = $AI->skin->vars['pagename'];

$replacements=array('[YYYY]'=>date('Y'));


if( ($path=ai_cascadepath('system/themes/'.$theme.'/pages/'.AI_PAGE_NAME.'-header.php'))!='' ) require($path);
else {
	$content = $AI->skin->get_tag_dynalist('footer');
	/*$options=array();
	$options['ok_types']=array('container','content','footer');
	$options['default_file']='system/themes/'.$theme.'/includes/footer_default.json';
	//DEFAULT IT WITH JSON? USE 'FOOTER.json' DEFAULT FROM THEME DIR?
	$content = $AI->get_dynalist('FOOTER-'.$theme,'widgets',$options);
	*/
	
	//REPLACEMENTS FOUND?
	if(strpos($content,'[[')!==false) {
		//MENU REPLACEMENT NEEDED?
		if(strpos($content,'[[site_menu]]')!==false) {
			//CATPURE THE NAV MENU
			$settings = array(
				'top_ul_id'=>'ai_ul_nav',	//draws like <ul id="ai_ul_nav">
				'top_ul_class'=>'ai_ul_menu',	//draws like <ul class="ai_ul_navmenu">
				'active_class'=>'active',
				'haschild_class'=>'haschild',
				'ischild_class'=>'ischild',
				'nav_depth_class'=>'navlevel',
				'draw_mobile_menu'=>1
				//'active_folders'=>array($l1_id=>$l1_id),
			);
			$replacements['[[site_menu]]'] = '<nav class="page-nav">' . $AI->get_menu('TopNav', 'dropdown_menu', -1, $settings) . '</div>';
		}

		if(strpos($content,'[[cleanup]]')!==false) {
			ob_start();
			require(ai_cascadepath('includes/cleanup_min.php'));
			$contents=ob_get_contents();
			ob_end_clean(); //end and write to output
			
			$replacements['[[cleanup]]'] = $contents;
		}

		//PERFORM THE REPLACEMENTS
		echo str_replace(array_keys($replacements),$replacements, $content);
	}
}



