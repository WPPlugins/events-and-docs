<?php
// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

$options = get_option( 'eventsndocs_settings' );
$keep_option = $options['on_uninstall'];
/** remembering keep options **/
/*
    0:  Keep events, settings and related posts
    1:  Delete events and settings, keep related posts
    2:  Delete events and settings, unpublish related posts
    3:  Delete events, settings and related posts'
*/

if ($keep_option == '0') return;

global $wpdb;

/*** unpublish or delete related docs ***/
$docs_ids = eventsndocs_getall_docs(); //IDs of related docs
if (!empty($docs_ids) && $keep_option == "2")
    $wpdb->query("
        UPDATE {$wpdb->posts} SET post_status='pending' WHERE ID in (". implode(',',$docs_ids) .")
    ");
if (!empty($docs_ids) && $keep_option == "3")
    $wpdb->query("
        DELETE FROM {$wpdb->posts} WHERE ID in (". implode(',',$docs_ids) .")
    ");
//get IDs of all post related with any event
function eventsndocs_getall_docs() {
    $query_args = array(
        'post_type' => 'post',
        'post_status'   => 'any',
        'posts_per_page' => -1,
        'no_found_rows' => true,
        'meta_query' => array(
            array(
                'key' => 'eventsndocs_id',
                'value' => -1,
                'compare' => '>',
                'type' => 'NUMERIC'
            )
        )
    );
    //get related docs
    $IDs = array();
    $query = new WP_Query( $query_args );
    if ( $query->have_posts() ) :
        while ( $query->have_posts() ) :
            $query->the_post();
            $IDs[] = get_the_ID();
        endwhile;
    endif;
    //reset data
    wp_reset_postdata();
    return $IDs;
}

/*** delete events and its settings ***/
// Delete custom post meta
delete_post_meta_by_key( 'eventsndocs_starts' );
delete_post_meta_by_key( 'eventsndocs_ends' );
delete_post_meta_by_key( 'eventsndocs_hide_date' );
delete_post_meta_by_key( 'eventsndocs_time' );
delete_post_meta_by_key( 'eventsndocs_location' );
delete_post_meta_by_key( 'eventsndocs_icoords' );
delete_post_meta_by_key( 'eventsndocs_izoom' );
delete_post_meta_by_key( 'eventsndocs_link' );
delete_post_meta_by_key( 'eventsndocs_link_target' );
delete_post_meta_by_key( 'eventsndocs_orgs' );
delete_post_meta_by_key( 'eventsndocs_sponsors' );
//
delete_post_meta_by_key( 'eventsndocs_id');

// Delete option
delete_option( 'eventsndocs_settings' );

// Delete terms
$wpdb->query("
    DELETE FROM {$wpdb->terms}
    WHERE term_id IN (
        SELECT * FROM (
            SELECT {$wpdb->terms}.term_id
            FROM {$wpdb->terms}
            JOIN {$wpdb->term_taxonomy}
            ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
            WHERE taxonomy = 'eventsndocs_cat'
        ) as T
    );
");

// Delete taxonomy
$wpdb->query("
    DELETE FROM {$wpdb->term_taxonomy} WHERE taxonomy = 'eventsndocs_cat'
");

// Delete Events
$wpdb->query("
    DELETE FROM {$wpdb->posts} WHERE post_type = 'eventsndocs'
");


?>
