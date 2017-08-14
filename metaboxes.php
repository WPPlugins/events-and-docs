<?php

// CREATE METABOXES //
function eventsndocs_metabox() {
    add_meta_box(
        'eventsndocs-event-metabox',
        __( 'Event Info', 'eventsndocs' ),
        'draw_eventsndocs_metabox',
        'eventsndocs', 'side', 'default'
    );
    add_meta_box(
        'eventsndocs-post-metabox',
        __('Post related with some Event'),
        'draw_eventsndocs_idbox',
        'post', 'side', 'core'
    );
}
add_action( 'add_meta_boxes', 'eventsndocs_metabox' );


// DRAW METABOXES //
function draw_eventsndocs_metabox( $post ) {
    // generate a nonce field
    wp_nonce_field( 'eventsndocs_meta_box', 'eventsndocs_nonce' );
    $ID = $post->ID;
    // get previously saved meta values (if any)
    $start_date = get_post_meta($ID, 'eventsndocs_starts', true);
    $end_date = get_post_meta($ID, 'eventsndocs_ends', true);
    $hide_date = get_post_meta($ID, 'eventsndocs_hide_date', true);
    $time = get_post_meta($ID, 'eventsndocs_time', true );
    $location = get_post_meta($ID, 'eventsndocs_location', true );
    $coords = get_post_meta($ID, 'eventsndocs_icoords', true );
    $zoom = get_post_meta($ID, 'eventsndocs_izoom', true );
    $link = get_post_meta($ID, 'eventsndocs_link', true );
    //$link_label = get_post_meta($ID, 'eventsndocs_link_label', true );
    $link_target =get_post_meta($ID,'eventsndocs_link_target',true);
    $orgs = get_post_meta($ID,'eventsndocs_orgs',true);
    $sponsors = get_post_meta($ID, 'eventsndocs_sponsors', true );
    // get date if saved else set it to current date
    $start_date = !empty($start_date) ? $start_date : time();
    $end_date = !empty($end_date) ? $end_date : time();
    // set dateformat to match datepicker
    //$options = get_option( 'eventsndocs_settings' );
    $options = eventsndocs_get_options();
    $dateformat = $options['date_format'];
    if (in_array($dateformat[0], array('j','d','S','l','D')))
        $dateformat = 'd-m-Y';
    else
        $dateformat = 'Y-m-d';
    //set init coords and zoom
    if (empty($coords)) $coords = $options['eventsndocs_icoords'];
    if (empty($coords)) $coords = "23.072097, -82.468600";
    if (empty($zoom)) $zoom = $options['eventsndocs_izoom'];
    if (intval($zoom) < 0 || intval($zoom) > 18 ) $zoom = '5';
    ?>
    <!-- metabox fields -->
    <br />
    <h4><?php _e('When','eventsndocs');?></h4>
    <p>
        <label for="eventsndocs_starts"><?php _e('Start date', 'eventsndocs'); ?></label>
        <input class="widefat" id="eventsndocs_starts" type="text" name="eventsndocs_starts" required maxlength="10" placeholder="<?php _e('Use datepicker','eventsndocs');?>" value="<?php echo date_i18n($dateformat, esc_attr($start_date)); ?>" />
    </p>
    <p>
        <label for="eventsndocs_ends"><?php _e( 'End date', 'eventsndocs' ); ?></label>
        <input class="widefat" id="eventsndocs_ends" type="text" name="eventsndocs_ends" required maxlength="10" placeholder="<?php _e('Use datepicker','eventsndocs');?>" value="<?php echo date_i18n($dateformat, esc_attr($end_date)); ?>" />
    </p>
    <p>
        <label for="eventsndocs_time"><?php _e('Time', 'eventsndocs'); ?></label>
        <input class="widefat" id="eventsndocs_time" type="text" name="eventsndocs_time" maxlength="100" placeholder="<?php _e('Example: 17:00 - 20:30','eventsndocs');?>" value="<?php echo esc_attr($time); ?>" />
    </p>
    <p>
        <input class="checkbox" id="eventsndocs_hide_date" type="checkbox" name="eventsndocs_hide_date" value="yes" <?php checked($hide_date, 'yes'); ?> />
        <label for="eventsndocs_hide_date"><?php _e('Hide date & time', 'eventsndocs'); ?></label>
    </p>
    <br />
    <h4><?php _e('Where','eventsndocs');?></h4>
    <p>
        <label for="eventsndocs_location"><?php _e('Location', 'eventsndocs'); ?></label>
        <input class="widefat" id="eventsndocs_location" type="text" name="eventsndocs_location" maxlength="100" placeholder="<?php _e('Example: La Habana, Cuba','eventsndocs');?>" value="<?php echo esc_attr($location); ?>" />
    </p>
    <div id="btn_geoloc"></div>
    <div id="map"></div>
    <p>
        <input id="eventsndocs_icoords" type="hidden" name="eventsndocs_icoords" maxlength="150" value="<?php echo $coords; ?>" />
        <input id="eventsndocs_izoom" type="hidden" name="eventsndocs_izoom" maxlength="2" value="<?php echo $zoom; ?>" />
        <label for="eventsndocs_link"><?php _e('Link', 'eventsndocs'); ?></label>
        <input class="widefat" id="eventsndocs_link" type="text" name="eventsndocs_link" maxlength="150" placeholder="<?php _e('Example: wordpress.org','eventsndocs');?>" value="<?php echo esc_url($link); ?>" />
    </p>
    <p>
        <input class="checkbox" id="eventsndocs_link_target" type="checkbox" name="eventsndocs_link_target" value="yes" <?php checked($link_target, 'yes'); ?> />
        <label for="eventsndocs_link_target"><?php _e('Open link in new window', 'eventsndocs'); ?></label>
    </p>
    <br />
    <h4><?php _e('Who','eventsndocs');?></h4>
    <span class="description"><?php _e ('One organizer or sponsor per line, in this way: the name, then an asterisk followed by the link (if needed), then an asterisk followed by the url of the logo (if preferred)', 'eventsndocs');?></span>
    <p>
        <label for="eventsndocs_orgs"><?php _e( 'Event Organizers', 'eventsndocs' ); ?></label>
        <textarea class="widefat" id="eventsndocs_orgs" name="eventsndocs_orgs" rows="3" maxlength="150" placeholder="<?php _e ('Example: OCDH * http://observacuba.org', 'eventsndocs');?>"><?php echo $orgs;?></textarea>
    </p>
    <p>
        <label for="eventsndocs_sponsors"><?php _e( 'Event Sponsors', 'eventsndocs' ); ?></label>
        <textarea class="widefat" id="eventsndocs_sponsors" name="eventsndocs_sponsors" rows="3" maxlength="150" placeholder="<?php _e ('Example: SPONSOR * http://imsponsor.com * http://imsponsor.com/logo.png', 'eventsndocs');?>"><?php echo $sponsors;?></textarea>
    </p>
    <?php
}

function draw_eventsndocs_idbox($post) {
    // generate a nonce field
    wp_nonce_field( 'eventsndocs_meta_box', 'eventsndocs_nonce' );
    //draw list of events on select form
    $eventsndocs_id = get_post_meta( $post->ID, 'eventsndocs_id', true );
    ?>
    <p><?php _e('Please, select an Event below if the current post is related with such event.', 'eventsndocs');?></p>
    <?php
    $query = new WP_Query( array('post_type' => 'eventsndocs','post_status' => 'publish','posts_per_page' => -1));
    if( $query->have_posts() ) {
        ?>
        <select name='eventsndocs_id' id='eventsndocs_id'>
            <option value = "-1"><?php _e( '(Post non related with any Event)', 'eventsndocs' ); ?></option>
            <?php
            while ($query->have_posts()) : $query->the_post();
                $value = get_the_ID();
                ?>
                <option value="<?php echo $value;?>" <?php if ($value == $eventsndocs_id) echo "selected";?>>
                    <?php echo get_the_title();?>
                </option>
                <?php
            endwhile;?>
        </select>
        <?php
    }else{
        _e( 'No Events created or published at this moment...' , 'eventsndocs');
    }
    wp_reset_query();
}


// SAVE METABOXES DATA //
function eventsndocs_save_event_info( $post_id ) {
    // check if nonce is set
    if ( ! isset( $_POST['eventsndocs_nonce'] ) ) return;
    // verify that nonce is valid
    if ( ! wp_verify_nonce( $_POST['eventsndocs_nonce'], 'eventsndocs_meta_box' ) ) return;
    // if this is an autosave, our form has not been submitted, so do nothing
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    // check user permissions
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;
    // checking values and save fields in custom post type EVENT
    //if (get_post( $post ) == 'event') :
    if (get_post_type($post) == 'eventsndocs') :
        if ( isset($_POST['eventsndocs_orgs']) )
            update_post_meta($post_id, 'eventsndocs_orgs',implode("\n",array_map('sanitize_text_field',explode("\n", $_POST['eventsndocs_orgs']))));
        if ( isset($_POST['eventsndocs_sponsors']) )
            update_post_meta( $post_id, 'eventsndocs_sponsors', implode("\n", array_map('sanitize_text_field', explode("\n", $_POST['eventsndocs_sponsors']))));
        if ( isset( $_POST['eventsndocs_starts'] ) )
            update_post_meta( $post_id, 'eventsndocs_starts', sanitize_text_field(strtotime( $_POST['eventsndocs_starts'] ) ) );
        if ( isset( $_POST['eventsndocs_ends'] ) )
            update_post_meta( $post_id, 'eventsndocs_ends', sanitize_text_field(strtotime( $_POST['eventsndocs_ends'] ) ) );
        if ( isset( $_POST['eventsndocs_time'] ) )
            update_post_meta( $post_id, 'eventsndocs_time', sanitize_text_field( $_POST['eventsndocs_time'] ) );
        if ( isset( $_POST['eventsndocs_location'] ) )
            update_post_meta( $post_id, 'eventsndocs_location', sanitize_text_field( $_POST['eventsndocs_location'] ) );
        if ( isset( $_POST['eventsndocs_icoords'] ) )
            update_post_meta( $post_id, 'eventsndocs_icoords', sanitize_text_field( $_POST['eventsndocs_icoords'] ) );
        if ( isset( $_POST['eventsndocs_izoom'] ) )
            update_post_meta( $post_id, 'eventsndocs_izoom', sanitize_text_field( $_POST['eventsndocs_izoom'] ) );
        if ( isset( $_POST['eventsndocs_link'] ) )
            update_post_meta( $post_id, 'eventsndocs_link', esc_url_raw( $_POST['eventsndocs_link'] ) );
        /*if ( isset( $_POST['eventsndocs_link_label'] ) )
            update_post_meta( $post_id, 'eventsndocs_link_label', sanitize_text_field( $_POST['eventsndocs_link_label'] ) );*/
        if ( isset( $_POST['eventsndocs_hide_date'] ) ) {
            update_post_meta( $post_id, 'eventsndocs_hide_date', 'yes' );
        } else {
            update_post_meta( $post_id, 'eventsndocs_hide_date', 'no' );
        }
        if ( isset( $_POST['eventsndocs_link_target'] ) ) {
            update_post_meta( $post_id, 'eventsndocs_link_target', 'yes' );
        } else {
            update_post_meta( $post_id, 'eventsndocs_link_target', 'no' );
        }
    endif;
    // checking values and save fields in regular posts
    if ( isset( $_POST['eventsndocs_id'] ) )
            update_post_meta( $post_id, 'eventsndocs_id',  $_POST['eventsndocs_id']  );
}
add_action( 'save_post', 'eventsndocs_save_event_info' );
?>
