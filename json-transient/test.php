<?php
/**
 * Created by PhpStorm.
 * User: cosmin
 * Date: 07/11/2018
 * Time: 14:45
 */
global $init;

var_dump($init);

$init->get_page_data_by_slug( $_GET['slug'], $_GET['type'] ) ;

// Return error on false.
if ( $cache === false ) {
    wp_send_json( 'Error, the page does not exist or it is not cached correctly. Please try rebuilding cache and try again!' );
}

// Decode json for output.
wp_send_json( json_decode( $cache ) );
