<?php
/**
 * Plugin Name: Events and Docs
 * Description: This is a simple plugin to display a list of events (OR events on a map), as well as documents related.
 * Version: 1.0
 * Author: Ernesto Ortiz
 * Author URI:
 * License: GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: eventsndocs
 * Domain Path: languages
 */

/** load plugin text domain **/
function eventsndocs_init() {
    load_plugin_textdomain( 'eventsndocs', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action('plugins_loaded', 'eventsndocs_init');


/** Enqueue styles & scripts **/
add_action('admin_enqueue_scripts', 'eventsndocs_backend_scripts');
add_action('wp_enqueue_scripts', 'eventsndocs_frontend_scripts');
function eventsndocs_frontend_scripts() {
    if(is_admin()) return;
    wp_enqueue_style('eventsndocs_style', plugins_url('/css/style.css',__FILE__));
    global $post_type;
    if ($post_type=="eventsndocs" || is_page() ) //improve later
        enqueue_leaflet();
}
function eventsndocs_backend_scripts() {
    if(!is_admin()) return;
    //LEAFLET map
    global $post_type;
    if ($post_type=="eventsndocs")
        enqueue_leaflet();
    //admin styles and scripts
    wp_enqueue_style('eventsndocs_admin_style', plugins_url('/css/admin_style.css',__FILE__));
    wp_register_script( 'backend_js', plugins_url('/js/backend.js',__FILE__), array('jquery'));
    wp_enqueue_script('backend_js');
}
if (!function_exists('enqueue_leaflet')) {
    function enqueue_leaflet(){
        //leaflet
        wp_enqueue_style('leaflet_style', 'http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css');
        wp_register_script( 'leaflet_js','http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js');
        wp_enqueue_script('leaflet_js');
        //leaflet fullscreen plugin
        wp_register_script( 'leaflet_fullscr','https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js', array('leaflet_js'));
        wp_enqueue_style('leaflet_fullscr_css', 'https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css');
        wp_enqueue_script('leaflet_fullscr');
        //backend scripts
        if (is_admin()):
             wp_register_script( 'map_backend',plugins_url('/js/map_backend.js',__FILE__), array('leaflet_js'));
            wp_enqueue_script('map_backend');
        //else:
          //  wp_register_script( 'map_frontend',plugins_url('/js/map_frontend.js',__FILE__), array('leaflet_js'));
          //  wp_enqueue_script('map_frontend');
        endif;

    }
}
// DATEPICKER //
function eventsndocs_datepicker_dateformat( $dateformat ) {
    if (in_array($dateformat[0], array('j','d','S','l','D')))
        $dateformat = 'dd-mm-yy';
    else
        $dateformat = 'yy-mm-dd';
    return $dateformat;
}
// enqueue datepicker script
function eventsndocs_enqueue_date_picker(){
    global $wp_locale, $post_type;
    if( 'eventsndocs' != $post_type ) return;
    wp_enqueue_script( 'eventsndocs_datepicker_script', plugins_url( '/js/datepicker.js' , __FILE__ ), array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), '1.0', true );
    wp_enqueue_style('eventsndocs_datepicker_style', plugins_url( '/css/datepicker.css',__FILE__));
    // datepicker args
    $options = get_option( 'eventsndocs_settings' );
    $eventsndocs_datepicker_args = array(
        'prevText' => __( 'Prev', 'eventsndocs' ),
        'nextText' => __( 'Next', 'eventsndocs' ),
        'monthNames' => array_values( $wp_locale->month ),
        'monthNamesShort' => array_values( $wp_locale->month_abbrev ),
        'dayNames' => array_values( $wp_locale->weekday ),
        'dayNamesShort' => array_values( $wp_locale->weekday_abbrev ),
        'dayNamesMin' => array_values( $wp_locale->weekday_initial ),
        'dateFormat' => eventsndocs_datepicker_dateformat($options['date_format'])
    );
    // localize script with data for datepicker
    wp_localize_script( 'eventsndocs_datepicker_script', 'objectL10n', $eventsndocs_datepicker_args );
}
add_action( 'admin_enqueue_scripts', 'eventsndocs_enqueue_date_picker' );


/** CUSTOM POST TYPE & DASHBOARD **/
include 'dashboard.php';

/** Draw METABOXES **/
global $post_type;
if ($eventsndocs == $post_type)
    include 'metaboxes.php';

/** OPTIONs PAGE **/
if (is_admin()) include 'optionspage.php';
/* DEFAULT option values */
function eventsndocs_get_options(){
    //$options = get_option('eventsndocs_settings');
    $defaults = array(
        'date_format' => 'j m, Y',
        'eventsndocs_icoords' => '23.072097, -82.468600',
        'eventsndocs_izoom' => '5',
        'fit_bounds' => true
    );
    $options = wp_parse_args(get_option('eventsndocs_settings'), $defaults);
    return $options;
}

/** SHORTCODES **/
include 'shortcodes.php';

/** TEMPLATES **/
//include 'single.php';

?>
