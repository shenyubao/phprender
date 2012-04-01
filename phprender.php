<?php 
if(isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 1 )
{
	$xh_open = true;
	sae_xhprof_start();
}
error_reporting(E_ALL);
include_once 'phprender.class.php';

$render = new Render('templates');
//$render->setCaching(true);
$variable = array('a'=>8,'b'=>4,'c'=>8,'name'=>'新浪无线','user'=>array('a','b','c','d','e','f','g'));
$render->index2($variable);

if( isset( $xh_open ) && $xh_open )
{
	sae_xhprof_end();
}
?>