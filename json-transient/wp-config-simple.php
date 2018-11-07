<?php
/**
 * Create simple wp configuration for the routes
 *
 * @since 1.0.0
 * @package json-transient
 */

define( 'SHORTINIT', true );
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once filter_var( $parse_uri[0] . 'wp-load.php', FILTER_SANITIZE_STRING );
