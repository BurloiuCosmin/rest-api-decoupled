<?php

/*
* Plugin Name:       init
* Description:       A brief description2
* Author URI:        https://infinum.co/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       json-transient
*/

namespace Json_Transient;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class Init {
    /**
     * Get the array of allowed types to do operations on.
     *
     * @return array
     *
     * @since 1.0.0
     */
    public function get_allowed_post_types() {
        return array( 'post', 'page' );
    }

    /**
     * Check if post type is allowed to be save in transient.
     *
     * @param string $post_type Get post type.
     * @return boolean
     *
     * @since 1.0.0
     */
    public function is_post_type_allowed_to_save( $post_type = null ) {
        if( ! $post_type ) {
            return false;
        }

        $allowed_types = $this->get_allowed_post_types();

        if ( in_array( $post_type, $allowed_types, true ) ) {
            return true;
        }

        return false;
    }

    /**
     * Get Page cache name for transient by post slug and type.
     *
     * @param string $post_slug Page Slug to save.
     * @param string $post_type Page Type to save.
     * @return string
     *
     * @since  1.0.0
     */
    public function get_page_cache_name_by_slug( $post_slug = null, $post_type = null ) {
        if( ! $post_slug || ! $post_type ) {
            return false;
        }

        $post_slug = str_replace( '__trashed', '', $post_slug );

        return 'jt_data_' . $post_type . '_' . $post_slug;
    }

    /**
     * Get full post data by post slug and type.
     *
     * @param string $post_slug Page Slug to do Query by.
     * @param string $post_type Page Type to do Query by.
     * @return array
     *
     * @since  1.0.0
     */
    public function get_page_data_by_slug( $post_slug = null, $post_type = null ) {
        if( ! $post_slug || ! $post_type ) {
            return false;
        }

        $page_output = '';

        $args = array(
            'name'           => $post_slug,
            'post_type'      => $post_type,
            'posts_per_page' => 1,
            'no_found_rows'  => true
        );

        $the_query = new \WP_Query( $args );

        if ( $the_query->have_posts() ) {
            while ( $the_query->have_posts() ) {
                $the_query->the_post();
                $page_output = $the_query->post;
            }
            wp_reset_postdata();
        }
        return $page_output;
    }

    /**
     * Return Page in JSON format
     *
     * @param string $post_slug Page Slug.
     * @param string $post_type Page Type.
     * @return json
     *
     * @since  1.0.0
     */
    public function get_json_page( $post_slug = null, $post_type = null ) {
        if( ! $post_slug || ! $post_type ) {
            return false;
        }

        return wp_json_encode( $this->get_page_data_by_slug( $post_slug, $post_type ) );
    }

    /**
     * Update Page to transient for caching on action hooks save_post.
     *
     * @param int $post_id Saved Post ID provided by action hook.
     *
     * @since 1.0.0
     */
    public function update_page_transient( $post_id ) {

        $post_status = get_post_status( $post_id );
        $post        = get_post( $post_id );
        $post_slug   = $post->post_name;
        $post_type   = $post->post_type;
        $cache_name  = $this->get_page_cache_name_by_slug( $post_slug, $post_type );

        if( ! $cache_name ) {
            return false;
        }

        if( $post_status === 'auto-draft' || $post_status === 'inherit' ) {
            return false;
        } else if( $post_status === 'trash' ) {
            delete_transient( $cache_name );
        } else  {
            if( $this->is_post_type_allowed_to_save( $post_type ) ) {
                $cache = $this->get_json_page( $post_slug, $post_type );
                set_transient( $cache_name, $cache, 0 );
            }
        }
    }
}

$init = new Init();

add_action( 'save_post', [ $init, 'update_page_transient' ] );
