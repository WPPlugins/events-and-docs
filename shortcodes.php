<?php

// CPTs added to the main query and feeds
add_action( 'pre_get_posts', 'eventsndocs_to_query' );
function eventsndocs_to_query( $query = '') {
    if ((is_home() && $query->is_main_query()) || is_feed())
        $query->set( 'post_type', array('post','eventsndocs'));
    return $query;
}
//shortcodes as widgets on text widget
add_filter('widget_text', 'do_shortcode');


/*          SHORTCODE
    ******* to show status and meta of the current event *******
*/
add_shortcode('eventsndocs_show_who','eventsndocs_shortcode_who');
function eventsndocs_shortcode_who( $atts ){
    //only on singular books
    if (!is_singular()) return false;
    global $post;
    //default args
    $args = shortcode_atts( array(
        'event_id' => $post->ID,
        'show' => 'o', //o,s for organizers, sponsors
        'title'=> '',
    ), $atts );
    //event
    $ID = $args['event_id'];
    if ($args['show']=="s"):
        $whos = get_post_meta($ID, 'eventsndocs_sponsors', true );
        $return = "<div id='eventsdocs_sponsors'>";
    else:
        $whos = get_post_meta($ID, 'eventsndocs_orgs', true );
        $return = "<div id='eventsdocs_orgs'>";
    endif;
    if (empty($whos)) return false;
    if ($args['title'])
        $return .= "<h4>".$args['title']."</h4>";
    $whichones = explode("\n", $whos);
    foreach ($whichones as $which){
        $data = explode("*", $which);
        $count = count($data);
        $return .= "<div>";
        if ($count > 1)
            $return .= "<a href='".$data[1]."' >";
        if ($count <= 2)
            $return .= $data[0];
        if ($count == 3)
            $return .= "<img src='".$data[2]."'  title='".$data[0]."' >";
        if ($count > 1)
            $return .= "</a>";
        $return .="</div>";
    }
    $return .="</div>";
    return $return;
}


/*          SHORTCODE
    ******* to show status and meta of the current event *******
*/
add_shortcode( 'eventsndocs_show_metas', 'eventsndocs_shortcode_metas' );
function eventsndocs_shortcode_metas( $atts ){
    global $post;
    //default args
    $args = shortcode_atts( array(
        'event_id' => $post->ID,
        'show' => 's,m', //s,m for status, meta
    ), $atts );
    //event
    $event_id = $args['event_id'];
    if ($event_id=="last" || $event_id=="current")
        $event_id = get_eventndocs_lastone();
    if (!$event_id) return false;
    //let's continue
    echo draw_eventsndocs_metas($event_id, $args['show']);
}
// return status of the event
function eventsndocs_get_status($event_id, $starts='', $ends=''){
    $status = "next";
    if (empty($starts)):
        $starts=get_post_meta($event_id,'eventsndocs_starts',true);
        $ends=get_post_meta($event_id,'eventsndocs_ends',true);
    endif;
    $cur_time = time();
    //status
    if ( $cur_time > $ends )
        $status = "ended";
    elseif ($cur_time > $starts)
        $status = "still";
    return $status;
}
// get all metas related to specific event id
function draw_eventsndocs_metas($event_id, $show='s,m') {
    //show meta or status
    $show_status = strpos(' '.$show, 's');
    $show_meta = strpos(' '.$show, 'm');
    //get post meta data
    $event_starts = get_post_meta( $event_id, 'eventsndocs_starts', true );
    $event_ends = get_post_meta( $event_id, 'eventsndocs_ends', true );
    $event_hide_date = get_post_meta( $event_id, 'eventsndocs_hide_date', true );
    $event_time = get_post_meta( $event_id, 'eventsndocs_time', true );
    $event_location = get_post_meta( $event_id, 'eventsndocs_location', true );
    $link = get_post_meta($event_id, 'eventsndocs_link', true );
    //$link_label = get_post_meta($event_id, 'eventsndocs_link_label', true );
    $link_target =get_post_meta($event_id,'eventsndocs_link_target',true);
    //vars
    $html = '';
    $status_label = __( 'Next Event', 'eventsndocs' );
    $status_class = '';
    $cur_time = time();
    //status
    $status = eventsndocs_get_status($event_id, $event_starts, $event_ends);
    if ( $status == "ended" ) {
        $status_class = " event_ended";
        $status_label = __( 'Event already ended ;(', 'eventsndocs' );
    } elseif ($status == "still") {
        $status_class = " event_still";
        $status_label = __( 'Event being developed ;)', 'eventsndocs' );
    }
    if ($show_status):
        $html .= '<div class="eventsndocs_status"><p';
        if ($status_class) $html .=' class="'. $status_class .'"';
        $html .= '>'. $status_label .'</p></div>';
    endif;
    //meta
    if ($show_meta):
        $html .= '<div class="eventsndocs_meta">';
        if ($event_hide_date != 'yes') {
           // $options = get_option( 'eventsndocs_settings' );
            $options = eventsndocs_get_options();
            $date_format = $options['date_format'];
            if (!empty($event_starts))
                $html .= '<p class="eventsndocs_meta_date'. $status_class .'">' . __( 'Start date: ', 'eventsndocs' ). '<span>'. date_i18n( $date_format, esc_attr($event_starts) ) . '</span></p>';
            if (!empty($event_ends))
                $html .= '<p class="eventsndocs_meta_date'. $status_class  .'">'. __( 'End date: ', 'eventsndocs' ). '<span>'. date_i18n( $date_format, esc_attr($event_ends) ) . '</span></p>';
            if(!empty($event_time) && $cur_time < $event_ends)
                $html .= '<p class="eventsndocs_meta_time">' . __( 'Time: ', 'eventsndocs' ). '<span>'. esc_attr($event_time)  . '</span></p>';
        }
        if(!empty($event_location)):
            $target='';
            if ($link_target) $target = 'target="_blank" ';
            $html .= '<p class="eventsndocs_meta_location">' . __( 'Location:', 'eventsndocs' ).'<span>';
            if (!empty($link))
                $html .= '<a href="'.$link.'" '.$target.'>';
            $html .= esc_attr($event_location);
            if (!empty($link)) $html .= '</a>';
            $html .= '</span></p>';
        endif;
        $html .= '</div>';
    endif;
    return '<div class="eventsndocs_metas">'. $html. '</div>';
}


/*          SHORTCODE
    ******* to show documents related to event *******
*/
add_shortcode( 'eventsndocs_show_docs', 'eventsndocs_shortcode_docs' );
function eventsndocs_shortcode_docs( $atts ){
    global $post;
    //default args
    $args = shortcode_atts( array(
        'event_id' => $post->ID,
        'show' => 'i,t,e', //t,i,a,e for title,image,author,excerpt
        'q' => -1
    ), $atts );
    //docs related to event
    $event_id = $args['event_id'];
    if ($event_id=="last" || $event_id=="current")
        $event_id = get_eventsndocs_lastone();
    if (!$event_id) return false;
    //let's continue
    echo draw_eventsndocs_docs($event_id, $args['show'], $args['q']);
}
// get all docs related to specific event id
function draw_eventsndocs_docs($event_id, $show='t,a,e', $q=-1) {
    $sorted_in_docs = docsnevents_sort($show);
    //get docs
    $content = "<div id='docsnevent_wrap'>";
    $args = array(
        'post_type' => array('post'),
        'no_found_rows' => true,
        'posts_per_page' => $q,
        'meta_query' => array(
            array(
                'key' => 'eventsndocs_id',
                'value' => $event_id,
                'compare' => '=',
            )
        )
    );
    $query = new WP_Query($args);
    if( $query->have_posts() ) {
        while ($query->have_posts()) : $query->the_post();
            $content .= "<div class='docsnevent'>";
            $a = "<a href='". get_the_permalink() ."'  title='". get_the_title()."' >";
            //docs
            foreach ($sorted_in_docs as $k=>$v){
                if ($k =='i')
                    if (has_post_thumbnail())
                        $content .= $a.get_the_post_thumbnail(null,'featured',array('class'=>'docsnevent_thumb') )."</a>";
                if ($k=='t')
                    $content .= $a."<h4>".get_the_title()."</h4></a>";
                if ($k == 'a')
                    $content .= "<h5>".get_the_author()."</h5>";
                if ($k == 'e')
                    $content .= "<p>".get_the_content(null,true)."</p>";
            }
            $content.= "</div>";
        endwhile;
    }else{
        $content .= "<p>".__('There are no documents related to this Event...')."</p>";
    }
    $content .= "</div>";
    wp_reset_postdata();
    //draw it
    return $content;
}
// get the right order
function docsnevents_sort($xtra){
    // values of t, i, a, e to show title, image, author or excerpt
    $show_title = strpos(' '.$xtra, 't');
    $show_img = strpos(' '.$xtra, 'i');
    $show_author = strpos(' '.$xtra, 'a');
    $show_excerpt = strpos($xtra, 'e');
    //define order
    $xtra_order = array();
    if ($show_title) $xtra_order['t'] = $show_title;
    if ($show_img) $xtra_order['i'] = $show_img;
    if ($show_author) $xtra_order['a'] = $show_author;
    if ($show_excerpt) $xtra_order['e'] = $show_excerpt;
    asort($xtra_order);
    return ($xtra_order);
}
// get event ID of last event
function get_eventsndocs_lastone(){
    $today = strtotime('today');
    $query_args = array(
        'post_type' => 'eventsndocs',
        'post_status'   => 'publish',
        'posts_per_page' => 1,
        'no_found_rows' => true,
        'meta_key' => 'eventsndocs_ends',
        'orderby' => 'meta_value_num',
        'order' => 'desc',
        'meta_query' => array(
            array(
                'key' => 'eventsndocs_starts',
                'value' => $today,
                'compare' => '<=',
                'type' => 'NUMERIC'
                )
        )
    );
    $query = new WP_Query( $query_args );
    if ( $query->have_posts() ) :
        while ( $query->have_posts() ) :
            $query->the_post();
            $event_id = get_the_ID();
        endwhile;
    else:
        $event_id = false;
    endif;
    //reset data
    wp_reset_postdata();
    return $event_id;
}


/*          SHORTCODE
    ******* to show the events *******
*/
add_shortcode('eventsndocs_show_events','eventsndocs_shortcode_events');
function eventsndocs_shortcode_events( $atts ){
    //default args
    $args = shortcode_atts( array(
        'type' => 'list', //list, map (at this moment)
        'xtra' => '', //'meta', 'status','docs' (add 'e' for define an order for event different than first)
        'show' => 'i,t,e', //t,a,i,e
        'showfordocs' => 't', //if empty and d in xtra === show
        'which' => 'all', //all, (current, last, further, next) + #n + rand
        //'in_catslug' => '', //in these categories (separated by comma)
        //'url' => '' //for banner
    ), $atts );
    // Number of events to get
    $q = -1; // all
    $which = explode(' ', trim($args['which']));
    $count = count($which);
    $abs_which = $which[0]; // all, next, current, last, further
    $last_which = $which[$count-1]; // (a,n,c,l,f) or #n or 'rand'
    if ($count == 1) $q = 1; // all, current, last, next
    if ($abs_which == 'all') $q = -1; // all
    if (is_int($last_which)) $q = $last_which; // current n,last n,next n
    if ($count > 1) $q = $which[1]; // (current n, last n, next n) + rand
    //see if show events randomly
    $rand = false;
    if ($last_which == 'rand') $rand = true;
    // Basic query of events
    $query_args = array(
        'post_type' => 'eventsndocs',
        'post_status'   => 'publish',
        'posts_per_page' => $q,
        'no_found_rows' => true,
    );
    // Order of Events (random or by start date)
    if ($rand):
        $query_args['orderby'] = 'rand';
    else:
        $query_args['meta_key'] = 'eventsndocs_starts';
        $query_args['orderby'] = 'meta_value_num';
        $query_args['order'] = 'desc';
    endif;
    // Time matters in Events
    $today = strtotime('today');
    //query for events in the future
    if ($abs_which == "next" || $abs_which == "further" )
        $time_query = array(
            array(
                'key' => 'eventsndocs_starts',
                'value' => $today,
                'compare' => '>',
                'type' => 'NUMERIC'
            )
        );
    //query for events in the past (until today)
    if ($abs_which == "last" ):
        $time_query = array(
            array(
                'key' => 'eventsndocs_starts',
                'value' => $today,
                'compare' => '<=',
                'type' => 'NUMERIC'
            )
        );
        $query_args['meta_key'] = 'eventsndocs_ends';
    endif;
    //query for events in the present
    if ($abs_which == "current" )
        $time_query = array(
            'relation' => 'AND',
            array(
                'key' => 'eventsndocs_starts',
                'value' => $today,
                'compare' => '<=',
                'type' => 'NUMERIC'
            ),
            array(
                'key' => 'eventsndocs_ends',
                'value' => $today,
                'compare' => '>=',
                'type' => 'NUMERIC'
            )
    );
    //change order
    if ($abs_which == "next") $query_args['order'] = 'asc';
    if ($abs_which == "current") $query_args['meta_key'] = 'eventsndocs_ends';
    //time query (in "next", "last", "further" or "current" )
    if(isset($time_query))
        $query_args['meta_query'] = $time_query;
    //get eventsndocs (and show meta, status, docs)
    $type = trim($args['type']);
    draw_eventsndocs_list ($query_args, $type, $args['xtra'], $args['show'], $args['showfordocs']);
}
//
//get all events, given the query_args
function draw_eventsndocs_list($query_args, $type='list', $xtra='', $show='i,t,e', $showfordocs = '') {
    //show xtras (meta, status and related docs) and event
    $show_meta = strpos(' '.$xtra, 'm');
    $show_status = strpos(' '.$xtra, 's');
    $show_docs= strpos(' '.$xtra, 'd');
    $show_event = strpos($xtra, 'e');
    if (!$show_event) $show_event = 0;
    //define order
    $xtra_order = array();
    if ($show_meta) $xtra_order['m'] = $show_meta;
    if ($show_status) $xtra_order['s'] = $show_status;
    if ($show_docs) $xtra_order['d'] = $show_docs;
    $xtra_order['e'] = $show_event;
    asort($xtra_order);
    //sort of what is showed in events or docs
    $sorted_in_events = docsnevents_sort($show);
    //vars
    $htmls = '';
    $metas = '';
    $docs = '';
    if ($type == "map"){ $e_coords=array();$e_layers=array(); }
    //get events
    $query = new WP_Query( $query_args );
    if ( $query->have_posts() ) :
        while ( $query->have_posts() ) :
            $query->the_post();
            $ID = get_the_ID();
            if ($type=="map"):
                $htmls = '';
                $e_status = eventsndocs_get_status($ID);
            endif;
            $htmls .= "<div class='eventsndocs_wrap'>";
            //event
            $events = "<div class='eventsndocs_event'>";
            $a = "<a href='". get_the_permalink() ."'  title='". get_the_title()."' >";
            foreach ($sorted_in_events as $k=>$v){
                if ($k =='i')
                    if (has_post_thumbnail())
                        $events .= $a. get_the_post_thumbnail(get_the_ID(),'post-image'). "</a>";
                if ($k == 't')
                    $events .= "<h2>".$a. get_the_title() ."</a></h2>";
                if ($k == 'a')
                    $events .= "<h5>". get_the_author() ."</h5>";
                if ($k == 'e')
                    $events .= "<p>". get_the_excerpt(). "</p>";
            }
            $events .= "</div>";
            //metas
            if ($show_meta || $show_status)
                $metas = draw_eventsndocs_metas($ID, $xtra);
            //docs
            if ($show_docs){
                if (empty($showfordocs)) $showfordocs = $show;
                $docs = draw_eventsndocs_docs($ID, $showfordocs);
            }
            //order of appereance (event, metas, docs)
            $already_metas = false;
            foreach ($xtra_order as $k=>$v){
                if ($k=="e") $htmls .= $events;
                if (!$already_metas && ($k=="s" || $k=="m")):
                    $htmls .= $metas;
                    $already_metas = true;
                endif;
                if ($k=="d") $htmls .= $docs;
            }
            $htmls .= "</div>";
            if ($type=="map"):
                $coords = get_post_meta($ID,'eventsndocs_icoords', true);
                if (empty($coords)) break;
                $coord = explode(",", $coords);
                $e_coords[] = array(trim($coord[0])*1, trim($coord[1])*1);
                $e_layers[$e_status][] = $htmls;
            endif;
        endwhile;
        //draw it
        if ($type=="map"):
            eventsndocs_on_map($e_coords, $e_layers);
        else:
            echo $htmls;
        endif;
        //
        wp_reset_postdata();
    else:
        if ($type!="map") echo __('No events found.', 'eventsndocs');
        else eventsndocs_no_map();
    endif;
    //reset data
    wp_reset_postdata();
}
//
function eventsndocs_on_map($e_coords, $e_layers){
    //count groups (still, ended, next)
    $mgroups = count($e_layers);
    //options
   // $options = get_option( 'eventsndocs_settings' );
    $options = eventsndocs_get_options();
    $fit = $options['fit_bounds'];
    if (empty($fit)) $fit = 0;
    $coords = $options['eventsndocs_icoords'];
    $zoom = $options['eventsndocs_izoom'];
    //if (empty($coords)) $coords="23.072097, -82.468600";
    if (empty($zoom) || $zoom<0 || $zoom > 18) $zoom="5";
    $latlng = explode(",", $coords);
    $lat = trim($latlng[0])*1; $lng = trim($latlng[1])*1;
    //SCRIPT
    ?>
    <script>
    jQuery(document).ready(function ($) {
        //tiles
        var tile_url = 'http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png';
        var tile_attrib = '<a href="http://www.mapbox.com/about/maps/" target="_blank">Terms &amp; Feedback</a>'
        var mapboxTiles = L.tileLayer( tile_url, {attribution: tile_attrib});
        //map
        var map = L.map('map', {
                fullscreenControl: {
                    pseudoFullscreen: false
                }
            })
            .addLayer(mapboxTiles)
            .setView([<?php echo $lat;?>, <?php echo $lng;?>], <?php echo $zoom*1;?>);
        //groups
        var map_groups = "<?php echo $mgroups;?>"*1;
        next_markers = []; still_markers = [];
        ended_markers = []; all_markers = [];
        //iterate
        <?php
        $m_count=0;
        foreach ($e_layers as $k=>$elayers){
            foreach ($elayers as $elayer){
                $coords = $e_coords[$m_count];
                $lat = $coords[0];
                $lng = $coords[1];
                ?>
                //marker
                var coord_marker = L.marker([<?php echo $lat;?>, <?php echo $lng;?>]);
                //add to map
                coord_marker //.addTo(map)
                    //bind
                    .bindPopup('<?php echo addslashes($elayer);?>')
                    .on('mouseover', function() {
                        this.openPopup();
                    });
                //CREATE GROUPs
                var group = "<?php echo $k;?>";
                if (group == "next") next_markers.push(coord_marker);
                if (group == "still") still_markers.push(coord_marker);
                if (group == "ended") ended_markers.push(coord_marker);
                all_markers.push(coord_marker);
                <?php
                $m_count++;
            }
        }
        ?>
        //groups on map
        var fit = '<?php echo $fit;?>'*1;
        if (null != next_markers)
            next_layers = L.featureGroup(next_markers);
        if (null != still_markers)
            still_layers = L.featureGroup(still_markers);
        if (null != ended_markers)
            ended_layers = L.featureGroup(ended_markers);
        if (null != all_markers){
            all_layers = L.featureGroup(all_markers);
            all_layers.addTo(map);
            if (fit){
                all_layers_bound = all_layers.getBounds();
                bounds = L.latLngBounds(all_layers_bound._southWest,all_layers_bound._northEast);
                map.fitBounds(bounds);
            }
        }
        //create filter
        var filter_dom = document.getElementById("map_filter");
        if (null != filter_dom && map_groups > 1){
            var btns = '<div id="filters" class="button-group">';
            if (0 != next_markers.length)
                btns = btns + '<button class="button efilter_next">'+'<?php echo __('Next events','eventsndocs'); ?>' + '</button>';
            if (0 != still_markers.length)
                btns = btns + '<button class="button efilter_still">'+'<?php echo __('Current events','eventsndocs'); ?>' + '</button>';
            if (0 != ended_markers.length)
                btns = btns + '<button class="button efilter_ended">'+'<?php echo __('Ended events','eventsndocs'); ?>' + '</button>';
            btns = btns + '<button class="button is-checked efilter_all">'+'<?php echo __('All events','eventsndocs'); ?>'+'</button></div>';
            //filter_dom.innerHTML(btns);
            $("#map_filter").html(btns);
        }else{
            $("#map_filter").hide();
        }
        //clickon filters buttons
        $(".efilter_ended").click(function(){
            show_map_events(map,ended_layers, this);
        })
        $(".efilter_still").click(function(){
            show_map_events(map,still_layers, this);
        })
        $(".efilter_next").click(function(){
            show_map_events(map,next_layers, this);
        })
        $(".efilter_all").click(function(){
            show_map_events(map,all_layers, this);
        })
        function show_map_events(map,layer, btn){
            $("#map_filter button").removeClass('is-checked');
            $(btn).addClass('is-checked');
            map.eachLayer(function (layers) {
                if (layers._layers)
                    map.removeLayer(layers);
            });
            map.addLayer(layer);
            if (fit)
                map.fitBounds(layer.getBounds(), {
                    padding: [50, 50]
                });
        }
    });
</script>
<?php
}
//
function eventsndocs_no_map(){
    $options = eventsndocs_get_options();
    $coords = $options['eventsndocs_icoords'];
    $zoom = $options['eventsndocs_izoom'];
    $latlng = explode(",", $coords);
    $lat = trim($latlng[0])*1; $lng = trim($latlng[1])*1;
    //SCRIPT
    ?>
    <script>
    jQuery(document).ready(function ($) {
        //tiles
        var tile_url = 'http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png';
        var tile_attrib = '<a href="http://www.mapbox.com/about/maps/" target="_blank">Terms &amp; Feedback</a>'
        var mapboxTiles = L.tileLayer( tile_url, {attribution: tile_attrib});
        //map
        var map = L.map('map', {
                fullscreenControl: {
                    pseudoFullscreen: false
                }
            })
            .addLayer(mapboxTiles)
            .setView([<?php echo $lat;?>, <?php echo $lng;?>], <?php echo $zoom*1;?>);
        //create filter
        var filter_dom = document.getElementById("map_filter");
        if (null != filter_dom ){
            var btns = '<div id="filters" class="button-group">';
            btns = btns + '<button class="button efilter_ended">'+'<?php echo __('NO events found...','eventsndocs'); ?>' + '</button>';
            //filter_dom.innerHTML(btns);
            $("#map_filter").html(btns);
        }else{
            $("#map_filter").hide();
        }
    });
</script>
<?php
}
