<?php
/*
* Plugin Name:       Documentation
* Description:       A brief description
* Author URI:        https://infinum.co/
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain:       test-plugin
*/

namespace Test_Plugin;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
die;
}

/**
* Class that holds all the necessary functionality for the
* documentation custom post type
*
* @since  1.0.0
*/
class Documentation {
/**
* The custom post type slug
*
* @var string
*
* @since 1.0.0
*/
const PLUGIN_NAME = 'documentation-plugin';

/**
* The custom post type slug
*
* @var string
*
* @since 1.0.0
*/
const POST_TYPE_SLUG = 'documentation';

/**
* The custom taxonomy type slug
*
* @var string
*
* @since 1.0.0
*/
const TAXONOMY_SLUG = 'documentation-category';

/**
* Register custom post type
*
* @since 1.0.0
*/
    public function register_post_type()
    {
        $args = array(
            'label' => esc_html('Documentation', 'test-plugin'),
            'public' => true,
            'menu_position' => 47,
            'menu_icon' => 'dashicons-book',
            'supports' => array('title', 'editor', 'revisions', 'thumbnail'),
            'has_archive' => true,
            'show_in_rest' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => true,
            'menu_position' => 6
        );

        register_post_type(self::POST_TYPE_SLUG, $args);
    }

/**
* Register custom tag taxonomy
*
* @since 1.0.0
*/
public function register_taxonomy() {
$args = array(
'hierarchical'          => false,
'label'                 => esc_html( 'Documentation tags', 'test-plugin' ),
'show_ui'               => true,
'show_admin_column'     => true,
'update_count_callback' => '_update_post_term_count',
'show_in_rest'          => true,
'query_var'             => true,
);

register_taxonomy( self::TAXONOMY_SLUG, [ self::POST_TYPE_SLUG ], $args );
}

    /**
     * Create a custom endpoint
     *
     * @since 1.0.0
     */
    public function create_custom_documentation_endpoint() {
        register_rest_route(
            self::PLUGIN_NAME . '/v1', '/custom-documentation',
            array(
                'methods'  => 'GET',
                'callback' => [ $this, 'get_custom_documentation' ],
            )
        );
    }

    /**
     * Create a callback for the custom documentation endpoint
     *
     * @return string                   JSON that indicates success/failure of the update,
     *                                  or JSON that indicates an error occurred.
     * @since 1.0.0
     */
    public function get_custom_documentation() {
        /* Some permission checks can be added here. */

        // Return only documentation name and tag name.
        $doc_args = array(
            'post_type'   => self::POST_TYPE_SLUG,
            'post_status' => 'publish',
            'perm'        => 'readable'
        );

        $query = new \WP_Query( $doc_args );

        $response = [];
        $counter  = 0;

        // The Loop
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();

                $post_id   = get_the_ID();
                $post_tags = get_the_terms( $post_id, self::TAXONOMY_SLUG );

                $response[ $counter ]['title'] = get_the_title();

                foreach ( $post_tags as $tags_key => $tags_value ) {
                    $response[ $counter ]['tags'][] = $tags_value->name;
                }
                $counter++;
            }
        } else {
            $response = esc_html__( 'There are no posts.', 'documentation-plugin' );
        }
        /* Restore original Post Data */
        wp_reset_postdata();

        return rest_ensure_response( $response );
    }
}

$documentation = new Documentation();

add_action( 'init', [ $documentation, 'register_post_type' ] );
add_action( 'init', [ $documentation, 'register_taxonomy' ] );
add_action( 'rest_api_init', [ $documentation, 'create_custom_documentation_endpoint' ] );





