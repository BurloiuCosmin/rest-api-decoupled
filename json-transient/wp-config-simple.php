<?php
/*
* Plugin Name:       init
* Description:       A brief description2
* Author URI:        https://infinum.co/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       json-transient
*/

define( 'SHORTINIT', true );
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
var_dump($parse_uri);
require_once filter_var( $parse_uri[0] . '/wp-load.php', FILTER_SANITIZE_STRING );

require_once 'init.php';