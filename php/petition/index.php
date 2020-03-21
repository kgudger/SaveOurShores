<?php
/*
Plugin Name: Petition
Plugin URI:  http://www.github.com/kgudger/SaveOurShores
Description: Petition short code
Version:     1.0
Author:      Keith Gudger
Author URI:  http://www.github.com/kgudger
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

//require_once("includes/dbstart.php");
include_once("./includes/header.php");
include_once("./includes/bpetitionpage.php");

/**
 * The checkArray defines what checkForm does so you don'tDnaSM4XIiCPEVCHD
 * have to overwrite it in the derived class. 
*/

$checkArray = array(
array("isLessThan0","reside", "Please enter a valid region."),
array("isEmpty","name", "Please enter your name."),
array("isInvalidEmail","email", "Please enter a valid email."),
array("isEmpty","street", "Please enter a street address."),
array("isEmpty","city", "Please enter a city."),
array("isEmpty","check", "Please agree to the petition."),
array("isNotZip","zip", "Please enter a correct zip code")
);
/// and ... start it up!
echo showContent();
include_once("./includes/footer.php");
/**
 * There are 2 choices for redirection dependent on the sessvar
 * above which one gets taken.
 * For this page, altredirect to download. */

?>



