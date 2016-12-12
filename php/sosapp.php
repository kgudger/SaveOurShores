<?php
/*
Plugin Name: Save Our Shores App
Plugin URI:  http://www.github.com/kgudger/SaveOurShores
Description: Web App for Save Our Shores
Version:     1.0
Author:      Keith Gudger
Author URI:  http://www.github.com/kgudger
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) or die( 'Ah ah ah, you didn\'t say the magic word' );

add_shortcode('sosapp', 'sosapp_index');
function sosapp_index() {
/**
 * starts app
 */
include_once("wp-content/plugins/sosapp/www/index.html");

}


