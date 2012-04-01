<?php
 /**
 * Example Application

 * @package Example-application
 */
if(isset( $_REQUEST['debug'] ) && $_REQUEST['debug'] == 1 )
{
	$xh_open = true;
	sae_xhprof_start();
}
error_reporting(E_ALL);
require('../libs/Smarty.class.php');

$path="saemc://templates_c";//使用MC Wrapper
$cache="saemc://cache";//使用MC Wrapper

mkdir($path);
mkdir($cache);


$smarty = new Smarty();
$smarty->compile_dir = $path; //设置编译目录
$smarty->cache_dir = $cache; //设置编译目录


//$smarty->force_compile = true;
//$smarty->debugging = true;
$smarty->caching = true;
$smarty->cache_lifetime = 120;

$smarty->assign("Name","Fred Irving Johnathan Bradley Peppergill",true);
$smarty->assign("FirstName",array("John","Mary","James","Henry"));
$smarty->assign("LastName",array("Doe","Smith","Johnson","Case"));
$smarty->assign("Class",array(array("A","B","C","D"), array("E", "F", "G", "H"),
	  array("I", "J", "K", "L"), array("M", "N", "O", "P")));

$smarty->assign("contacts", array(array("phone" => "1", "fax" => "2", "cell" => "3"),
	  array("phone" => "555-4444", "fax" => "555-3333", "cell" => "760-1234")));
$smarty->assign("option_values", array("NY","NE","KS","IA","OK","TX"));
$smarty->assign("option_output", array("New York","Nebraska","Kansas","Iowa","Oklahoma","Texas"));
$smarty->assign("option_selected", "NE");
@$smarty->display('index.tpl');

if( isset( $xh_open ) && $xh_open )
{
	sae_xhprof_end();
}
?>
