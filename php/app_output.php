<?php
/*
Plugin Name: App Output
Plugin URI:  http://www.github.com/kgudger/SaveOurShores
Description: Displays lost of data from DB for iOS and Android apps
Version:     1.0
Author:      Keith Gudger
Author URI:  http://www.github.com/kgudger
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) or die( 'Ah ah ah, you didn\'t say the magic word' );

add_shortcode('app_output', 'dbsort_app_out');
function dbsort_app_out() {
/**
 * dbstart.php opens the database and gets the user variables
 */
require_once("includes/dbstart.php");

include_once("wp-content/plugins/app_output/includes/dbappoutpage.php");

/**
 * The checkArray defines what checkForm does so you don't
 * have to overwrite it in the derived class. */

$checkArray = array();
/// a new instance of the derived class (from MainPage)
$dbsort = new dbAppOutPage($db,$sessvar,$checkArray) ;
/// and ... start it up!  
$dbsort->main("SOS DataBase Output", $uid, "", "dfile.php");
/**
 * There are 2 choices for redirection dependent on the sessvar
 * above which one gets taken.
 * For this page, altredirect to download. */
}


