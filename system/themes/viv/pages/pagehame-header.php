<?php
//Example of override 'header' content for page 'pagename'

global $AI;
$theme = $AI->skin->vars['theme'];
$page = $AI->skin->vars['pagename'];

/*
Some Options:


1) Draw the normal DL Layout, use the "PHP Script" widget and place your logic in "includes/scripts/scriptname.php", and don't use this overried file


2) Draw custom logic (and no dynamic lists)
//add whatever PHP logic here


3) Draw Dynamic list with custom replacement logic (also custom logic before/after DL layout):

//add before logic here

$content = $AI->skin->get_tag_dynalist('footer');
//optional replacement logic here (see option 3 for examples)
  $replacements = array('[[replace_this_1]]'=>$value1, '[[replace_this_2]]'=>$value2)
	echo str_replace(array_keys($replacements),$replacements, $content);
//or just "echo $content;" 

//add draw-after logic here

*/ 