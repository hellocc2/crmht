<?
error_reporting(E_ALL);
ini_set("display_errors", true);
/* Set to true while testing */
//$debug = false;

/* While testing use the TEST merchantcode and password */
//$merchantCode = "yourmerchantcode";
//$merchantPassword = "yourmerchantpassword";

$merchantCode = "MILANOOUSD";
$merchantPassword = "Cu8rUyeD";

include("bibit.func.php");
include("Bibit.php");

$_bibit = new Bibit($debug);

$_bibit->merchantCode = $merchantCode;
$_bibit->merchantPassword = $merchantPassword;
?>