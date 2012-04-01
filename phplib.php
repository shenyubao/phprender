<?php
if(isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 1 )
{
	$xh_open = true;
	sae_xhprof_start();
}
require 'phplib.class.php';

$t = new Template("templates");
$t->set_file(".", "phplib.html");
$t->set_block('.','notice','notice_block');
$t->set_var('notice_block');
$t->set_block('.','notice2','notice2_block');
$t->set_var('notice2_block');
	

for($i=0;$i<10;$i++)
{
	$t->set_var('_NAME_','新浪无线');
	$t->parse('notice_block', 'notice',true);	
}


for($i=0;$i<10;$i++)
{
	$t->set_var('_NAME_','新浪微博');
	$t->parse('notice2_block', 'notice2',true);	
}
$t->pparse('cout','.');

if( isset( $xh_open ) && $xh_open )
{
	sae_xhprof_end();
}
?>