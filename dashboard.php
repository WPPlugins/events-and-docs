<?php

/*** CREATE CUSTOM POST TYPE ***/
function eventsndocs_custom_postype() {
    $eventsndocs_labels = array(
        'name' => __( 'Events', 'eventsndocs' ),
        'all_items' => __( 'All Events', 'eventsndocs' ),
        'singular_name' => __( 'Event', 'eventsndocs' ),
        'add_new' => __( 'Add New', 'eventsndocs' ),
        'add_new_item' => __( 'Add New Event', 'eventsndocs' ),
        'edit_item' => __( 'Edit Event', 'eventsndocs' ),
        'new_item' => __( 'New Event', 'eventsndocs' ),
        'view_item' => __( 'View Event', 'eventsndocs' ),
        'search_items' => __( 'Search Events', 'eventsndocs' ),
        'not_found' => __( 'No events found', 'eventsndocs' ),
        'not_found_in_trash' => __( 'No events found in Trash', 'eventsndocs' ),
    );
    $eventsndocs_args = array(
        'label' => __( 'Events', 'eventsndocs' ),
        'labels' => $eventsndocs_labels,
        'public' => true,
        'can_export' => true,
        'show_in_nav_menus' => false,
        'show_ui' => true,
        'capability_type' => 'post',
        'taxonomies' => array('eventsndocs_cat'),
        'supports'=> array('title', 'thumbnail', 'editor'),
    );
    register_post_type( 'eventsndocs', $eventsndocs_args);
}
add_action( 'init', 'eventsndocs_custom_postype' );

/** create event categories **/
function eventsndocs_taxonomy() {
    register_taxonomy( 'eventsndocs_cat', 'eventsndocs', array( 'label' => __( 'Event Categories', 'eventsndocs' ), 'hierarchical' => true, ) );
}
add_action( 'init', 'eventsndocs_taxonomy' );


/*** DASHBOARD EVENT COLUMNS ***/
function eventsndocs_custom_columns( $defaults ) {
    unset( $defaults['date'] );
    $defaults['eventsndocs_starts'] = __( 'Start date', 'eventsndocs' );
    $defaults['eventsndocs_ends'] = __( 'End date', 'eventsndocs' );
    $defaults['eventsndocs_location'] = __( 'Location', 'eventsndocs' );
    return $defaults;
}
add_filter('manage_eventsndocs_posts_columns','eventsndocs_custom_columns',10);

function eventsndocs_custom_columns_content( $column_name, $post_id ) {
    $options = get_option( 'eventsndocs_settings' );
    $date_format = $options['date_format'];
    $columns_dates = array('eventsndocs_starts', 'eventsndocs_ends');
    foreach ($columns_dates as $column_date)
        if ( $column_date == $column_name ) {
            $value = get_post_meta( $post_id, $column_date, true );
            if(!empty($value)) echo date_i18n( $date_format, $value );
        }
    if ( 'eventsndocs_location' == $column_name )
        echo get_post_meta( $post_id, 'eventsndocs_location', true );
}
add_action( 'manage_eventsndocs_posts_custom_column', 'eventsndocs_custom_columns_content', 10, 2 );

// make event date column sortable
function eventsndocs_columns_sortable( $columns ) {
    $columns['eventsndocs_starts'] = 'eventsndocs_sarts';
    $columns['eventsndocs_ends'] = 'eventsndocs_ends';
    return $columns;
}
add_filter( 'manage_edit-eventsndocs_sortable_columns', 'eventsndocs_columns_sortable' );
// and order by...
function eventsndocs_starts_column_orderby( $vars ) {
    if(is_admin())
        if (isset($vars['orderby']) && 'eventsndocs_starts'==$vars['orderby'])
            $vars = array_merge($vars, array(
                'meta_key' => 'eventsndocs_starts',
                'orderby' => 'meta_value_num'
            ));
    return $vars;
}
add_filter( 'request', 'eventsndocs_starts_column_orderby' );
//
function eventsndocs_ends_column_orderby( $vars ) {
    if(is_admin())
        if (isset($vars['orderby']) && 'eventsndocs_ends'==$vars['orderby'])
            $vars = array_merge($vars, array(
                'meta_key' => 'eventsndocs_ends',
                'orderby' => 'meta_value_num'
            ));
    return $vars;
}
add_filter( 'request', 'eventsndocs_ends_column_orderby' );

?>
