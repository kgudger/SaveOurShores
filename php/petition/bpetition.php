<?php
/*
Plugin Name: bpetition
Plugin URI:  http://www.github.com/kgudger/SaveOurShores
Description: Bottle Ban Petition
Version:     1.0
Author:      Keith Gudger
Author URI:  http://www.github.com/kgudger
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) or die( 'Ah ah ah, you didn\'t say the magic word' );

add_shortcode('bpetition', 'bpetition_f');
function bpetition_f() {
/**
 * dbstart.php opens the database and gets the user variables
 */
require_once("/var/www/html/includes/dbstartwp.php");
wp_enqueue_script( 'lang_script' );

include_once("/var/www/html/wp-content/plugins/bpetition/includes/petitionpage.php");

/**
 * The checkArray defines what checkForm does so you don't
 * have to overwrite it in the derived class. */

$checkArray = array(
array("isLessThan0","reside", "Please enter a valid region."),
array("isEmpty","name", "Please enter your name."),
array("isInvalidEmail","email", "Please enter a valid email."),
array("isEmpty","street", "Please enter a street address."),
array("isEmpty","city", "Please enter a city."),
array("isEmpty","check", "Please agree to the petition."),
array("isNotZip","zip", "Please enter a correct zip code")
);

/// a new instance of the derived class (from MainPage)
$petit = new petitionPage($db,"no",$checkArray) ;
/// and ... start it up!
//return $petit->main("SOS Petition", $uid, "", "");
ob_start();
echo $petit->main("SOS Petition", $uid, "", "");
$outp = ob_get_clean();
return $outp;

/**
 * There are 2 choices for redirection dependent on the sessvar
 * above which one gets taken.
 * For this page, altredirect to download. */
}

function lang_assets() {
	wp_register_script( 'lang_script', plugins_url() . '/bpetition/includes/lang.js', array() );
}

add_action( 'wp_enqueue_scripts', 'lang_assets' );
