<?php

/** Add plugin options page **/
add_action( 'admin_menu', 'eventsndocs_menu' );
function eventsndocs_menu() {
    // Options' page with title, menu title, capability, slug and function
    add_options_page( 'EVENTSnDOCs Plugin Options', 'Events and Docs', 'manage_options', 'eventsndocs', 'eventsndocs_options' );
}

/** Set options form **/
function eventsndocs_options() {
    if ( !current_user_can( 'manage_options' ) )
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    ?>
    <div id = "optionspage" class="wrap">
        <h2><?php echo __('EVENTSnDOCs Plugin Options', 'eventsndocs');?></h2>
        <br/>
        <form action="options.php" method="post">
            <?php
            settings_fields('eventsndocs_ffields');
            do_settings_sections('eventsndocs_sections');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/** Set form fields **/
add_action( 'admin_init', 'eventsndocs_setfields' );
function eventsndocs_setfields(  ) {
    register_setting( 'eventsndocs_ffields', 'eventsndocs_settings');
    /* sections */
    $section_titles = array(
        __( 'Date Format', 'eventsndocs' ),
        __( 'Map Settings', 'eventsndocs' ),
        __( 'On Uninstall', 'eventsndocs' )
    );
    //settings section
    foreach ($section_titles as $k=>$section_title)
        add_settings_section(
            'eventsndocs_section_'.($k+1),
            $section_title,
            'eventsndocs_section_callback',
            'eventsndocs_sections'
        );
    //section callback
    function eventsndocs_section_callback(  ) {
        return false;
    }

    /* fields */
    $field_names = array(
        'date_format',
        'coords',
        'on_uninstall'
    );
    $field_titles = array(
        __( 'Preferred date format', 'eventsndocs' ),
        __( 'Settings of the map', 'eventsndocs' ),
        __( 'Choose what to do...', 'eventsndocs' )
    );
    //setting fields
    foreach ($field_names as $k=>$field_name)
        add_settings_field(
            $field_name,
            $field_titles[$k],
            'eventsndocs_field_'.($k+1).'_callback',
            'eventsndocs_sections',
            'eventsndocs_section_'.($k+1)
        );
}


/** Draw fields' content **/
function eventsndocs_field_1_callback() {
    $options = eventsndocs_get_options();
    $value = $options['date_format'];
    ?>
    <input type='text' name='eventsndocs_settings[date_format]' value='<?php echo $value; ?>'>
    <p class="description"><?php echo __( 'Write your preferred PHP date format (for example: <em>j m, Y</em>)', 'eventsndocs' );?></p>
    <?php
}
function eventsndocs_field_2_callback() {
    $options = eventsndocs_get_options();
    $coords = $options['eventsndocs_icoords'];
    $zoom = $options['eventsndocs_izoom'];
    $fit = $options['fit_bounds'];
    //if (empty($coords)) $coords="23.072097, -82.468600";
    //if (empty($zoom)) $zoom="5";
    if (empty($zoom) || $zoom<0 || $zoom > 18) $zoom="5";
    if (empty($fit)) $fit="0";
    ?>
    <h4><?php echo __('Initial Coordinates', 'eventsndocs')?></h4>
    <input type='text' name='eventsndocs_settings[eventsndocs_icoords]' value='<?php echo $coords; ?>'>
    <p class="description"><?php echo __( 'Write your preferred <em>lat,lng</em> initial coordinates to show on empty post\'s maps (for example: <em>23.072097, -82.468600</em>)', 'eventsndocs' );?></p>
    <br />
    <h4><?php echo __('Initial Zoom', 'eventsndocs')?></h4>
    <input type='text' name='eventsndocs_settings[eventsndocs_izoom]' value='<?php echo $zoom; ?>'>
    <p class="description"><?php echo __( 'Write your preferred initial zoom to show on empty post\'s maps (<em>from 0 to 18</em>)', 'eventsndocs' );?></p>
    <br />
    <input type='checkbox' name='eventsndocs_settings[fit_bounds]' <?php checked( $fit, 1 ); ?> value='1'>
    <label><em><?php echo __('Instead, always zoom to fit all markers', 'eventsndocs');?></em></label>
    <p class="description"><?php echo __( 'Fit the map to the bounds of markers.', 'eventsndocs' );?></p>
    <?php
}
function eventsndocs_field_3_callback() {
    $options = eventsndocs_get_options();
    $value = $options['on_uninstall'];
    $keep_options = array(
        __( 'Keep events, settings and related posts', 'eventsndocs' ),
        __( 'Delete events and settings, keep related posts', 'eventsndocs' ),
        __( 'Delete events and settings, unpublish related posts', 'eventsndocs' ),
        __( 'Delete events, settings and related posts', 'eventsndocs' )
    )
    ?>
    <select name='eventsndocs_settings[on_uninstall]'>
        <?php
        foreach ($keep_options as $key=>$keep_option){ ?>
            <option value='<?php echo $key;?>' <?php selected( $value, $key ); ?>><?php echo $keep_option;?></option>
        <?php
        }
        ?>
    </select>
    <p class="description"><?php echo __( 'Choose what to do once uninstalled this plugin', 'eventsndocs' );?></p>
    <?php
}
